<?php
/**
 * WP Courseware Gateway Stripe - Subscription.
 *
 * @package WPCW
 * @subpackage Gateways\Stripe
 * @since 4.3.0
 */
namespace WPCW\Gateways\Stripe;

use Stripe\Charge;
use Stripe\Customer;
use Stripe\Invoice;
use Stripe\Plan;
use Stripe\Product;
use Stripe\Source;
use Stripe\Subscription;
use WPCW\Gateways\Gateway_Stripe;
use WPCW\Models\Course;
use WPCW\Models\Order;
use WPCW\Models\Order_Item;

// Exit if accessed directly
defined( 'ABSPATH' ) || die;

/**
 * Class Stripe_Subscription
 *
 * @since 4.3.0
 */
class Stripe_Subscription {

	/**
	 * @var Customer The stripe customer.
	 * @since 4.3.0
	 */
	protected $customer;

	/**
	 * @var Source The stripe source.
	 * @since 4.3.0
	 */
	protected $source;

	/**
	 * @var Order The order object.
	 * @since 4.3.0
	 */
	protected $order;

	/**
	 * @var Order_Item The order item object.
	 * @since 4.3.0
	 */
	protected $order_item;

	/**
	 * @var Course The course object.
	 * @since 4.3.0
	 */
	protected $course;

	/**
	 * @var Product The stripe product object.
	 * @since 4.3.0
	 */
	protected $product;

	/**
	 * @var Plan The stripe plan object.
	 * @since 4.3.0
	 */
	protected $plan;

	/**
	 * @var Subscription The stripe subscription object.
	 * @since 4.3.0
	 */
	protected $subscription;

	/**
	 * @var Invoice The stripe invoice object.
	 * @since 4.3.0
	 */
	protected $invoice;

	/**
	 * @var Gateway_Stripe The stripe payment gateway.
	 * @since 4.3.0
	 */
	protected $gateway;

	/**
	 * @var Order The payment order.
	 * @since 4.3.0
	 */
	protected $payment;

	/**
	 * Stripe_Subscription constructor.
	 *
	 * @since 4.3.0
	 *
	 * @param Customer $customer The customer object.
	 * @param Gateway_Stripe The stripe gateway.
	 */
	public function __construct( $customer, $gateway ) {
		$this->customer = $customer;
		$this->gateway  = $gateway;
	}

	/**
	 * Create Subscription.
	 *
	 * @since 4.3.0
	 *
	 * @param Order      $order The order object.
	 * @param Order_Item $order_item The order item object.
	 * @param Source     $source The stripe source object.
	 *
	 * @return string The subscription id.
	 */
	public function create_subscription( Order &$order, Order_Item $order_item, Source $source ) {
		$this->order      = $order;
		$this->order_item = $order_item;
		$this->source     = $source;

		// Get Course.
		$this->course = $this->order_item->get_course();

		// Check the course to see if its a subscription.
		if ( ! $this->course->is_subscription() ) {
			throw new Stripe_Exception( 'course-is-not-a-subscription', sprintf( __( '%1$s ( Course ID: %2$s ) - is not a subscription. Subscription not created.', 'wp-courseware' ), $this->course->get_course_title(), $this->course->get_course_id() ) );
		}

		// Check for customer.
		if ( empty( $this->customer ) ) {
			throw new Stripe_Exception( 'customer-is-not-set', esc_html__( 'The customer needed to process a subscription is empty. Subscription not created.', 'wp-courseware' ) );
		}

		// Create Stripe Plan.
		$this->maybe_create_plan();

		// Create Subscription.
		$subscription = new \WPCW\Models\Subscription();
		$subscription->create();

		// Check one more time, just in case.
		if ( ! $subscription || ! $subscription->get_id() ) {
			throw new Stripe_Exception( 'subscription-setup-error', sprintf( __( 'Subscription Setup Error for stripe subscription Id: %s', 'wp-courseware' ), $stripe_sub_id ) );
		}

		// Item Discount.
		$item_discount   = $this->order_item->get_discount();
		$discount_coupon = '';

		// Populate Item Discount.
		if ( $item_discount > 0 ) {
			$item_discount = $this->order_item->get_discount();

			$discount_coupon = $this->gateway->get_api()->create_coupon( array(
				'id'              => 'ONE_TIME_DISCOUNT_' . $this->order_item->get_id() . '_' . $order->get_order_id(),
				'duration'        => 'once',
				'amount_off'      => wpcw_round( $item_discount * 100 ),
				'currency'        => wpcw_get_currency(),
				'max_redemptions' => 1,
			) );

			// Check for error.
			if ( ! empty( $discount_coupon->error ) ) {
				throw new Stripe_Exception( $discount_coupon->error->type, $discount_coupon->error->message );
			}
		}

		// Create Stripe Subscription.
		$subscription_args = array(
			'customer'       => $this->customer->id,
			'default_source' => $this->source->id,
			'items'          => array(
				array(
					'plan'     => $this->get_plan()->id,
					'quantity' => 1,
				),
			),
			'metadata'       => array(
				'order'            => $order->get_order_id(),
				'subscription_id'  => $subscription->get_id(),
				'student_id'       => $order->get_student_id(),
				'student_email'    => $order->get_student_email(),
				'parent_order_id'  => $order->get_order_id(),
				'installment_plan' => $this->course->charge_installments(),
			),
		);

		// Apply coupon if exists.
		if ( ! empty( $discount_coupon ) ) {
			$subscription_args['coupon'] = $discount_coupon->id;
		}

		// Subscription Taxes
		if ( wpcw_taxes_enabled() ) {
			$subscription_args['tax_percent'] = wpcw_get_tax_percentage();
		}

		/**
		 * Filter: Stripe Subscription Args.
		 *
		 * @since 4.3.0
		 *
		 * @param array $subscription_args The subscription args.
		 * @param Customer The stripe customer object.
		 * @param Source The stripe source object.
		 * @param Plan The stripe source object.
		 * @param Product The stripe product object.
		 *
		 * @return array $subscription_args The modified subscription arguments.
		 */
		$subscription_args = apply_filters( 'wpcw_stripe_subscription_args', $subscription_args, $this->customer, $this->source, $this->plan, $this->product );

		// Create a Subscription.
		$this->subscription = $this->gateway->get_api()->create_subscription( $subscription_args );

		// Check for error.
		if ( ! empty( $this->subscription->error ) ) {
			throw new Stripe_Exception( $this->subscription->error->type, $this->subscription->error->message );
		}

		// Log the subscription.
		$this->log( sprintf( 'Subscription created successfully. Subscription ID: %s', $this->subscription->id ) );
		$this->log( sprintf( 'Subscription Data: %s', wpcw_print_r( $this->subscription->getLastResponse()->json, true ) ) );

		// Set Properties.
		$subscription->set_props( array(
			'student_id'       => $order->get_student_id(),
			'student_name'     => $order->get_student_full_name(),
			'student_email'    => $order->get_student_email(),
			'order_id'         => $order->get_order_id(),
			'method'           => $order->get_payment_method(),
			'profile_id'       => $this->get_id(),
			'created'          => date( 'Y-m-d H:i:s', $this->subscription->current_period_start ),
			'expiration'       => date( 'Y-m-d H:i:s', $this->subscription->current_period_end ),
			'installment_plan' => $this->course->charge_installments(),
		) );

		// Set Course Id.
		$subscription->set_prop( 'course_id', $order_item->get_course_id() );
		$subscription->set_prop( 'course_title', $order_item->get_order_item_title() );

		// Set Amounts and Period.
		$subscription->set_props( array(
			'initial_amount'   => ( $this->get_amount() ) ? $this->gateway->format_stripe_amount( $this->get_amount() ) : $order_item->get_amount(),
			'recurring_amount' => ( $this->get_amount() ) ? $this->gateway->format_stripe_amount( $this->get_amount() ) : $order_item->get_amount(),
			'period'           => ( $this->get_interval() ) ? $this->get_interval() : $this->get_course()->get_payments_interval(),
		) );

		// Create Subscription Payment.
		$payment = $subscription->create_payment( true );

		// Get Subscription Payment.
		$subscription_payment        = $this->gateway->get_api()->get_subscription_payment( $this->subscription->id );
		$subscription_transaction_id = '';

		// Check for the payment and make sure its paid, if not handle it in the webhook.
		if ( empty( $subscription_payment->error ) && false !== $subscription_payment && $subscription_payment instanceof Charge && $subscription_payment->paid ) {
			// Subscription Id.
			$subscription_transaction_id = $subscription_payment->id;

			// If is installment the 'bill_times' attribute gets set to 1 to record the initial payment.
			if ( $subscription->is_installment_plan() ) {
				$subscription->set_prop( 'bill_times', 1 );
				$payment->update_meta( '_installment_payment', true );
				$payment->update_meta( '_installment_payment_number', 1 );
			}

			// Store Fees.
			if ( isset( $subscription_payment->balance_transaction ) && isset( $subscription_payment->balance_transaction->fee ) ) {
				$amount = ! empty( $subscription_payment->balance_transaction->amount ) ? $this->gateway->format_balance_fee( $subscription_payment->balance_transaction, 'amount' ) : 0.00;
				$payment->update_meta( '_stripe_amount', $amount );

				$fee = ! empty( $subscription_payment->balance_transaction->fee ) ? $this->gateway->format_balance_fee( $subscription_payment->balance_transaction, 'fee' ) : 0.00;
				$payment->update_meta( '_stripe_fee', $fee );

				$net = ! empty( $subscription_payment->balance_transaction->net ) ? $this->gateway->format_balance_fee( $subscription_payment->balance_transaction, 'net' ) : 0.00;
				$payment->update_meta( '_stripe_net', $net );

				$currency = ! empty( $subscription_payment->balance_transaction->currency ) ? strtoupper( $subscription_payment->balance_transaction->currency ) : null;
				$payment->update_meta( '_stripe_currency', $currency );

				// Customer Id.
				if ( ! empty( $subscription_payment->customer ) ) {
					$payment->update_meta( '_stripe_customer_id', $subscription_payment->customer );
				}

				// Source Id.
				if ( ! empty( $subscription_payment->source->id ) ) {
					$payment->update_meta( '_stripe_source_id', $subscription_payment->source->id );
				}

				$payment->add_order_note( sprintf( __( 'Recorded Stripe Fees. Total Amount: %1$s, Fee: %2$s, Net Amount: %3$s, Currency: %4$s', 'wp-courseware' ), wpcw_price( $amount ), wpcw_price( $fee ), wpcw_price( $net ), $currency ) );
			}
		}

		// Set Subscription Id.
		$payment->set_prop( 'subscription_id', $subscription->get_id() );

		// Set Transaction Id.
		if ( empty( $subscription_transaction_id ) ) {
			$subscription_transaction_id = 'initial-payment-' . $this->get_id();
		}

		// Set Transaction Id.
		$subscription->set_prop( 'transaction_id', $subscription_transaction_id );

		// Set Status.
		if ( $this->get_status() === 'active' ) {
			$activated_message = $subscription->is_installment_plan()
				? sprintf( __( 'Installment Subscription Plan #%1$s activated. Stripe Installment Subscription Plan ID: %2$s', 'wp-courseware' ), $subscription->get_id(), $this->get_id() )
				: sprintf( __( 'Subscription #%1$s activated. Stripe Subscription Plan ID: %2$s', 'wp-courseware' ), $subscription->get_id(), $this->get_id() );

			$subscription->set_status( 'active', $activated_message );
		} else {
			$on_hold_message = $subscription->is_installment_plan()
				? sprintf( __( 'Installment Subscription Plan #%1$s pending payment. Stripe Installment Subscription Plan ID: %2$s', 'wp-courseware' ), $subscription->get_id(), $this->get_id() )
				: sprintf( __( 'Subscription #%1$s pending payment. Stripe Subscription Plan ID: %2$s', 'wp-courseware' ), $subscription->get_id(), $this->get_id() );

			$subscription->set_status( 'on-hold', $on_hold_message );
		}

		// Save Subscription.
		if ( $subscription->save() ) {
			$saved_message_log = $subscription->is_installment_plan()
				? sprintf( __( 'Installment Subscription #%1$s saved successfully. Installment Subscription Data: %2$s', 'wp-courseware' ), $subscription->get_id(), wpcw_print_r( $subscription->get_data(), true ) )
				: sprintf( __( 'Subscription #%1$s saved successfully. Subscription Data: %2$s', 'wp-courseware' ), $subscription->get_id(), wpcw_print_r( $subscription->get_data(), true ) );

			$this->log( $saved_message_log );
		} else {
			$error_message_log = $subscription->is_installment_plan()
				? sprintf( __( 'Subscription #%1$s save error. Subscription Data: %2$s', 'wp-courseware' ), $subscription->get_id(), wpcw_print_r( $subscription->get_data(), true ) )
				: sprintf( __( 'Subscription #%1$s save error. Subscription Data: %2$s', 'wp-courseware' ), $subscription->get_id(), wpcw_print_r( $subscription->get_data(), true ) );

			$this->log( $error_message_log );
		}

		// Set Status.
		if ( $subscription->has_status( 'active' ) ) {
			$payment_complete_message = $subscription->is_installment_plan()
				? sprintf( __( 'Installment Payment #%s is complete. Stripe Txn Id: %s', 'wp-courseware' ), $payment->get_order_number(), $subscription_transaction_id )
				: sprintf( __( 'Subscription Payment #%s is complete. Stripe Txn Id: %s', 'wp-courseware' ), $payment->get_order_number(), $subscription_transaction_id );

			$payment->payment_complete( $subscription_transaction_id, $payment_complete_message );
		} else {
			$on_hold_status_message = $subscription->is_installment_plan()
				? sprintf( __( 'Installment Payment #%s is pending payment. Stripe Txn Id: %s', 'wp-courseware' ), $payment->get_order_number(), $subscription_transaction_id )
				: sprintf( __( 'Subscription Payment #%s is pending payment. Stripe Txn Id: %s', 'wp-courseware' ), $payment->get_order_number(), $subscription_transaction_id );

			$payment->update_status( 'on-hold', $on_hold_status_message );
		}

		// Set Payment Order.
		$this->payment = $payment;

		return $this->get_id();
	}

	/**
	 * Get Stripe Subscription.
	 *
	 * @since 4.3.0
	 *
	 * @return bool|Subscription The subscription object or false on failure.
	 */
	public function get_subscription() {
		if ( empty( $this->subscription->id ) ) {
			return false;
		}

		return $this->subscription;
	}

	/**
	 * Get Plan Id.
	 *
	 * @since 4.3.0
	 *
	 * @return string The plan id.
	 */
	protected function get_plan_id() {
		if ( $this->order_item->use_installments() ) {
			$plan_id = sprintf(
				'%s-%s-installments-of-%s-plan',
				sanitize_title( $this->order_item->get_order_item_title() ),
				strtolower( $this->course->get_installments_interval_label() ),
				$this->course->get_installments_amount()
			);
		} else {
			$plan_id = sprintf(
				'%s-%s-%s-plan',
				sanitize_title( $this->order_item->get_order_item_title() ),
				$this->course->get_payments_price(),
				$this->course->get_payments_interval()
			);
		}

		return sanitize_key( $plan_id );
	}

	/**
	 * Get Plan Args.
	 *
	 * @since 4.3.0
	 *
	 * @return array The plan arguments.
	 */
	protected function get_plan_args() {
		// Intervals
		$interval       = $this->course->get_payments_interval();
		$interval_count = 1;

		// Modify for quarter and semi-year.
		switch ( $interval ) {
			case 'quarter' :
				$interval       = 'month';
				$interval_count = 3;
				break;
			case 'semi-year' :
				$interval       = 'month';
				$interval_count = 6;
				break;
		}

		$desc = $this->gateway->get_statement_desc();

		if ( $this->order_item->use_installments() ) {
			$name = sprintf(
				__( '%s - [%s Installments of %s]', 'wp-courseware' ),
				$this->order_item->get_order_item_title(),
				$this->course->get_installments_interval_label(),
				html_entity_decode( wpcw_price( $this->course->get_installments_amount() ), ENT_QUOTES, get_bloginfo( 'charset' ) )
			);
		} else {
			$name = sprintf(
				__( '%s - [%s %s]', 'wp-courseware' ),
				$this->order_item->get_order_item_title(),
				html_entity_decode( wpcw_price( $this->course->get_payments_price() ), ENT_QUOTES, get_bloginfo( 'charset' ) ),
				$this->course->get_subscription_interval()
			);
		}

		$amount = $this->gateway->get_total( $this->course->get_payments_price() );

		if ( empty( $desc ) ) {
			$desc = $this->gateway->format_statement_desc( strtoupper( wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) ) );
		}

		return apply_filters( 'wpcw_stripe_plan_args', array(
			'id'                   => $this->get_plan_id(),
			'name'                 => $name,
			'amount'               => $amount,
			'currency'             => wpcw_get_currency(),
			'interval'             => $interval,
			'interval_count'       => $interval_count,
			'statement_descriptor' => $desc,
			'metadata'             => array(
				'course_title'     => $this->course->get_course_title(),
				'course_id'        => $this->course->get_course_id(),
				'use_installments' => $this->course->charge_installments(),
			),
		) );
	}

	/**
	 * Maybe Create Plan.
	 *
	 * @since 4.3.0
	 *
	 * @return Plan|object The stripe plan object or an error object on failure.
	 */
	protected function maybe_create_plan() {
		if ( empty( $this->order_item ) ) {
			return;
		}

		if ( ! empty( $this->plan ) && $this->plan instanceof Plan ) {
			return $this->plan;
		}

		try {
			$this->plan = $this->gateway->get_api()->get_plan( $this->get_plan_id() );
			$currency   = strtolower( wpcw_get_currency() );

			if ( ! empty( $this->plan->error ) ) {
				throw new Stripe_Exception( $this->plan->error->type, $this->plan->error->message );
			}

			if ( strtolower( $this->plan->currency ) !== $currency ) {
				$plan_id   = $this->get_plan_id() . '_' . $currency;
				$plan_args = $this->get_plan_args();

				try {
					$this->plan = $this->gateway->get_api()->get_plan( $this->get_plan_id() );

					if ( ! empty( $this->plan->error ) ) {
						throw new Stripe_Exception( $this->plan->error->type, $this->plan->error->message );
					}
				} catch ( Stripe_Exception $exception ) {
					$plan_args['id'] = $plan_id;
					$this->plan      = $this->create_plan( $plan_args );

					if ( ! empty( $this->plan->error ) ) {
						throw new Stripe_Exception( $this->plan->error->type, $this->plan->error->message );
					}
				}
			}
		} catch ( Stripe_Exception $exception ) {
			$this->plan = $this->create_plan();

			if ( ! empty( $this->plan->error ) ) {
				throw new Stripe_Exception( $this->plan->error->type, $this->plan->error->message );
			}
		}
	}

	/**
	 * Create Plan.
	 *
	 * @since 4.3.0
	 *
	 * @return Plan The stripe subscription plan.
	 */
	protected function create_plan( $args = array() ) {
		$args = wp_parse_args( $args, $this->get_plan_args() );
		$id   = md5( serialize( $args ) );

		try {
			$this->product = $this->gateway->get_api()->get_product( $id );

			if ( ! empty( $this->product->error ) ) {
				throw new Stripe_Exception( $this->product->error->type, $this->product->error->message );
			}
		} catch ( Stripe_Exception $exception ) {
			$product_args = array(
				'id'   => $id,
				'name' => $args['name'],
				'type' => 'service',
			);

			if ( ! empty( $args['statement_descriptor'] ) ) {
				$product_args['statement_descriptor'] = $args['statement_descriptor'];
			}

			if ( ! empty( $args['metadata'] ) ) {
				$product_args['metadata'] = $args['metadata'];
			}

			$this->product = $this->gateway->get_api()->create_product( $product_args );

			if ( ! empty( $this->product->error ) ) {
				return $this->product;
			}
		}

		if ( ! empty( $this->product ) ) {
			$args['product'] = $this->product->id;
			unset( $args['name'], $args['statement_descriptor'] );

			$this->plan = $this->gateway->get_api()->create_plan( $args );

			if ( ! empty( $this->plan->error ) ) {
				return $this->plan;
			}
		}

		return $this->plan;
	}

	/**
	 * Get Plan.
	 *
	 * @since 4.3.0
	 *
	 * @return Plan The plan object.
	 */
	public function get_plan() {
		if ( empty( $this->plan ) ) {
			$this->maybe_create_plan();
		}

		return $this->plan;
	}

	/**
	 * Get Subscription Id.
	 *
	 * @return bool|string The subscription id if set, false otherwise.
	 */
	public function get_id() {
		if ( empty( $this->subscription ) || ! $this->subscription instanceof Subscription ) {
			return false;
		}

		return $this->subscription->id;
	}

	/**
	 * Get Subscription Plan Amount.
	 *
	 * @since 4.3.0
	 *
	 * @return bool|string The subscription plan amount or false on failure.
	 */
	public function get_amount() {
		if ( empty( $this->plan->id ) ) {
			return false;
		}

		return $this->plan->amount;
	}

	/**
	 * Get Subscription Plan Interval.
	 *
	 * @since 4.3.0
	 *
	 * @return bool|string The subscription plan interval or false on failure.
	 */
	public function get_interval() {
		if ( empty( $this->plan->interval ) ) {
			return false;
		}

		// Intervals
		$interval       = $this->plan->interval;
		$interval_count = $this->plan->interval_count;

		// Adjustment for quarter
		if ( 'month' === $interval && 3 === absint( $interval_count ) ) {
			$interval = 'quarter';
		}

		// Adjustment for semi-year
		if ( 'month' === $interval && 6 === absint( $interval_count ) ) {
			$interval = 'semi-year';
		}

		return $interval;
	}

	/**
	 * Get Subscription Status.
	 *
	 * @since 4.3.0
	 *
	 * @return bool|string $status The subscription status or false on failure.
	 */
	public function get_status() {
		if ( empty( $this->subscription->id ) ) {
			return false;
		}

		return $this->subscription->status;
	}

	/**
	 * Get Subscription Course.
	 *
	 * @since 4.3.0
	 *
	 * @return bool|Course The course object or false on failure.
	 */
	public function get_course() {
		if ( ! $this->course->get_course_id() ) {
			return false;
		}

		return $this->course;
	}

	/**
	 * Get Payment.
	 *
	 * @since 4.3.0
	 *
	 * @return Order $payment The payment order.
	 */
	public function get_payment() {
		if ( empty( $this->payment ) ) {
			return false;
		}

		return $this->payment;
	}

	/**
	 * Log Message.
	 *
	 * @since 4.3.0
	 *
	 * @param string $message The message text.
	 */
	protected function log( $message = '' ) {
		$this->gateway->log( $message );
	}
}
