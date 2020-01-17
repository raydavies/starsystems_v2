<?php
/**
 * WP Courseware Quiz Multiple Choice Question Type.
 *
 * @package WPCW
 * @since 1.0.0
 */

if ( ! class_exists( 'WPCW_quiz_MultipleChoice' ) ) {
	/**
	 * Class WPCW_quiz_MultipleChoice.
	 *
	 * The class that represents a multiple-choice question.
	 *
	 * @since 1.0.0
	 */
	class WPCW_quiz_MultipleChoice extends WPCW_quiz_base {
		public $answerListRaw;

		/**
		 * Default constructor.
		 *
		 * @param Object $quizItem The quiz item details.
		 */
		public function __construct( $quizItem ) {
			parent::__construct( $quizItem );
			$this->questionType = 'multi';
			$this->cssClasses   = 'wpcw_question_type_multi';

			$this->hint = __( '(Optional) Use this to guide the user that they should make a selection.', 'wp-courseware' );

			if ( $this->disabled ) {
				$this->cssClasses .= ' wpcw_question_disabled';
			}
		}

		/**
		 * Output the form that allows questions to be configured.
		 */
		public function editForm_toString() {
			$answerList = false;
			if ( $this->quizItem->question_data_answers ) {
				$answerList = WPCW_quizzes_decodeAnswers( $this->quizItem->question_data_answers );
			}

			$html = false;

			// Extra CSS for errors
			$errorClass_Question  = false;
			$errorClass_CorAnswer = false;

			// Error Check - Have we got an issue with a lack of question?
			if ( $this->showErrors ) {
				if ( ! $this->quizItem->question_question ) {
					$errorClass_Question = 'wpcw_quiz_missing';
					$this->gotError      = true;
				}
				if ( $this->needCorrectAnswers && ! $this->quizItem->question_correct_answer ) {
					$errorClass_CorAnswer = 'wpcw_quiz_missing';
					$this->gotError       = true;
				}
			}

			// Track columns needed to show question details
			$columnCount = 4;

			// Render just the question area
			$html .= sprintf( '<li id="wpcw_quiz_details_%s" class="%s"><table class="wpcw_quiz_details_questions_wrap" cellspacing="0">', $this->quizItem->question_id, $this->cssClasses );

			// Details of the question - top of the question details.
			$html .= $this->getSection_processHeader( $columnCount );

			// Check for being disabled.
			if ( $this->disabled ) {
				$html .= $this->getSection_disabledQuestionNotice( $columnCount );
			}

			// Main question details here...
			$html .= sprintf( '<tr class="wpcw_quiz_row_question %s">', $errorClass_Question );

			$html .= sprintf( '<th>%s</th>', __( 'Question', 'wp-courseware' ) );

			$html .= sprintf( '<td>' );
			$html .= sprintf( '<textarea name="question_question_%s">%s</textarea>', $this->quizItem->question_id, htmlspecialchars( $this->quizItem->question_question ) );
			$html .= sprintf( '<input type="hidden" name="question_type_%s" value="multi" />', $this->quizItem->question_id );

			// Field storing order of question among other questions
			$html .= sprintf(
				'<input type="hidden" name="question_order_%s" value="%s" class="wpcw_question_hidden_order" />', $this->quizItem->question_id, absint( $this->quizItem->question_order )
			);

			$html .= sprintf( '</td>' );

			// Only show column if need correct answers.
			$html .= sprintf( '<td class="wpcw_quiz_details_tick_correct wpcw_quiz_only_td">%s</td>', __( 'Correct<br/>Answer?', 'wp-courseware' ) );

			// Column for add/remove buttons
			$html .= '<td>&nbsp;</td>';

			$html .= sprintf( '</tr>' );

			// Render the section that allows an image to be shown.
			$html .= $this->getSection_showImageField( $columnCount );

			// Render the field that allows answers to be randomized
			$html .= $this->getSection_showRandomizeAnswersField( $columnCount );

			// Render the list of answers if we have any.
			if ( $answerList ) {
				$count = 0;
				$odd   = true;
				foreach ( $answerList as $answerItem ) {
					// Extract image if available
					$answerItemImageVal = WPCW_arrays_getValue( $answerItem, 'image' );

					// Exract the answer if available
					$answerItemVal = trim( $answerItem['answer'] );
					++ $count;
					/*
					 * Store the selected result.
					 * @var string
					 */
					$result          = $this->quizItem->question_correct_answer;
					$correct_answers = @unserialize( $result );
					/**
					 * check is the serialised array or not if not set $correct_answers to result value for the single answer quiz.
					 */
					if ( ! $correct_answers ) {
						$correct_answers = array( $result );
					}

					// Show an error if the field is still blank.
					$errorClass_Answer = false;
					if ( $this->showErrors ) {
						// Check that answer contains some characters.
						if ( strlen( $answerItemVal ) == 0 ) {
							$errorClass_Answer = 'wpcw_quiz_missing';
							$this->gotError    = true;
						}
					}

					// Add 'data-answer-id' field to store the ID of this row, and other rows that match this.
					$html .= sprintf( '<tr class="wpcw_quiz_row_answer %s %s" data-answer-id="%d">', $errorClass_Answer, ( $odd ? 'alternate' : '' ), $count );
					$html .= sprintf( '<th>%s <span>%d</span></th>', __( 'Answer', 'wp-courseware' ), $count );
					$html .= sprintf( '<td><input type="text" name="question_answer_%s[%d]" value="%s" /></td>', $this->quizItem->question_id, $count, htmlspecialchars( $answerItemVal ) );

					// Correct answer column
					$html .= sprintf( '<td class="wpcw_quiz_details_tick_correct wpcw_quiz_only_td">
                                        <input type="checkbox" name="question_answer_sel_%s[]" id="question_answer_sel_%s[]" value="%s" %s />
                                      </td>', $this->quizItem->question_id, $this->quizItem->question_id, $count, ( in_array( $count, $correct_answers ) ? 'checked="checked"' : false ) );

					// Buttons for add/remove questions
					$html .= sprintf(
						'
					<td class="wpcw_quiz_add_rem">
						<a href="#" title="%s" class="wpcw_question_add"><img src="%sicon_add_32.png" /></a>
						<a href="#" title="%s" class="wpcw_question_remove"><img src="%sicon_remove_32.png" /></a>
					</td>', __( 'Add a new answer...', 'wp-courseware' ), WPCW_IMG_URL, __( 'Remove this answer...', 'wp-courseware' ), WPCW_IMG_URL
					);

					$html .= sprintf( '</tr>' );

					// Add the image URL for this answer - added as a new row.
					$html .= sprintf( '<tr class="wpcw_quiz_row_answer_image wpcw_quiz_row_answer_image_%d %s %s">', $count, $errorClass_Answer, ( $odd ? 'alternate' : '' ) );
					$html .= sprintf(
						'<th>%s <span class="wpcw_inner_hint">%s</span></th>', __( 'Answer Image URL', 'wp-courseware' ), __( '(Optional) ', 'wp-courseware' )
					);

					$html .= '<td>';
					// Field name - needs to use underscore, as square brackets break the jQuery to find the target.
					$thisAnswerFieldName = 'question_answer_image_' . $this->quizItem->question_id . '_' . $count;

					// The URL field.
					$html .= sprintf(
						'<input type="text" name="question_answer_image_%s[%d]" id="%s" value="%s" />', $this->quizItem->question_id, $count, $thisAnswerFieldName, $answerItemImageVal
					);

					// The insert button.
					$html .= sprintf(
						'<span class="wpcw_insert_image_wrap"><a href="#" class="button wpcw_insert_image" data-uploader_title="%s" data-uploader_btn_text="%s" data-target="%s" title="%s"><span class="wpcw_insert_image_img"></span> %s</a></span>', __( 'Choose an image for this answer...', 'wp-courseware' ), __( 'Select Image...', 'wp-courseware' ), $thisAnswerFieldName, __( 'Select Image', 'wp-courseware' ), __( 'Select Image', 'wp-courseware' )
					);

					$html .= '</td>';

					// Filler for the remaining space
					$html .= '<td colspan="2"></td>';

					$html .= sprintf( '</tr>' );

					$odd = ! $odd;
				}
			}

			// Extra fields at the bottom of a question.
			$html .= $this->getSection_processFooter( $columnCount );

			// All done
			$html .= sprintf( '</table></li>' );

			return $html;
		}

		/**
		 * Determine if the answers need to be randomized.
		 */
		public function processAnswersWithRandomOption( $unit_id, $quiz_id ) {
			global $wpcwdb, $wpdb;
			$wpdb->show_errors();

			// Randomization is not needed.
			if ( ! $this->quizItem->question_multi_random_enable ) {
				return;
			}

			// Get current user ID
			$currentUserID = get_current_user_id();
			// Get Quiz Details
			$unitQuizProgress = WPCW_quizzes_getUserResultsForQuiz( $currentUserID, $unit_id, $quiz_id );

			$possibleAnswers = false;
			// Do we have progress yet?
			if ( $unitQuizProgress && $unitQuizProgress->quiz_grade < 0 ) {
				$possibleAnswers = $unitQuizProgress->quiz_data[ $this->quizItem->question_id ]['possible_answers'];
			}

			// Force the possible answers to be retrieved instead of providing a new random set
			if ( $possibleAnswers ) {
				$possibleAnswers = maybe_unserialize( $possibleAnswers );
				//Need list copy to manipulate, as we don't want to modify the raw list just yet.
				$listCopy   = $this->answerListRaw;
				$newRawList = array();
				foreach ( $possibleAnswers as $key => $value ) {
					if ( isset( $listCopy[ $value ] ) ) {
						$newRawList[ $value ] = $listCopy[ $value ];
						unset( $listCopy[ $value ] );
					}
				}
				$this->answerListRaw = $newRawList;
			} else {
				// Need list copy to manipulate, as we don't want to modify the raw list just yet.
				$listCopy   = $this->answerListRaw;
				$newRawList = array();
				// Extract the correct answer from the serialised array of the answers
				$correctAnswerIndexArray = @unserialize( $this->quizItem->question_correct_answer );
				if ( ! $correctAnswerIndexArray ) {
					$correctAnswerIndexArray = array( $this->quizItem->question_correct_answer );
				}
				foreach ( $correctAnswerIndexArray as $key => $value ) {
					if ( isset( $listCopy[ $value ] ) ) {
						// Copy the right answer, and then remove from the source list
						$newRawList[ $value ] = $listCopy[ $value ];
						unset( $listCopy[ $value ] );
					}
				}

				// Set seed to the current user ID so that sequence is predictable. Don't really need a
				// lock for this.
				//srand(time() + get_current_user_id());

				srand( time() );

				// Now we need to copy all the answers that the user wants.
				while ( count( $newRawList ) < $this->quizItem->question_multi_random_count && ! empty( $listCopy ) ) {
					// Get a random item from the copy
					$randomKey = array_rand( $listCopy, 1 );

					// Copy the right answer, and then remove from the source list
					$newRawList[ $randomKey ] = $listCopy[ $randomKey ];
					unset( $listCopy[ $randomKey ] );
				}

				// Randomize the ordering (reset the seed to something new)
				//srand(time());

				srand();
				$newRawList = WPCW_arrays_shuffle_assoc( $newRawList );

				// And update the raw list for rendering.
				$this->answerListRaw = $newRawList;
			}


		}

		/**
		 * Render Form.
		 *
		 * @see WPCW_quiz_base::renderForm_toString()
		 */
		public function renderForm_toString( $parentQuiz, $questionNum, $selectedAnswer, $showAsError, $errorToShow = false ) {
			// Process all answers to give them an index. Count must be 1 indexed to avoid disappearing
			// due to 0 evaluating to false.
			if ( $this->quizItem->question_data_answers ) {
				// Extract answers into raw format.
				$this->answerListRaw = WPCW_quizzes_decodeAnswers( $this->quizItem->question_data_answers );
				// If the user has requested the answers to be randomized, then use this. This function
				// with automatically check and handle the randomization and update $this->answerListRaw.
				$this->processAnswersWithRandomOption( $parentQuiz->parent_unit_id, $parentQuiz->quiz_id );

				// Got answers, so break up into a list of answer => value
				if ( $this->answerListRaw ) {
					$this->answerList      = array();
					$this->answerImageList = array();
					foreach ( $this->answerListRaw as $idx => $answerItem ) {
						$answerKey = 'ans_' . $idx;

						// Reversing the answer value to key here..
						$this->answerList[ trim( $answerItem['answer'] ) ] = $answerKey;

						// Store the image if we have one.
						if ( isset( $answerItem['image'] ) ) {
							$this->answerImageList[ $answerKey ] = $answerItem['image'];
						}
					}
				}
			} // end of answer check
			// Add the hint if there is one
			if ( $this->quizItem->question_answer_hint ) {
				$this->extraQuizHTMLAfter .= sprintf( '<div class="wpcw_fe_quiz_q_hint">%s</div>', nl2br( htmlspecialchars( $this->quizItem->question_answer_hint ) ) );
			}
			if ( is_array( $selectedAnswer ) ) {
				foreach ( $selectedAnswer as $key => $value ) {
					$selectedAnswer[ $key ] = 'ans_' . $value;
				}
			} else {
				$selectedAnswer = 'ans_' . $selectedAnswer;
			}

			// Handover to parent. All multiple choice answers are prefixed with 'ans_'.
			return parent::renderForm_toString_withClass( $parentQuiz, $questionNum, $selectedAnswer, $showAsError, 'wpcw_fe_quiz_q_multi', $errorToShow );
		}

		/**
		 * Extract the list of correct answers for a Multiple Choice question when saving changes to a question,
		 * using the specified answer key to check $_POST.
		 *
		 * @param String $answerListKey The key to use to extract the list of answers.
		 * @param String $answerImageListKey The key to use to extract the list of answer images.
		 *
		 * @return String The list of answers, if found.
		 */
		public static function editSave_extractAnswerList( $answerListKey, $answerImageListKey ) {
			$qAns = array();

			// ### 1 - Get the list of answers if we have them
			if ( isset( $_POST[ $answerListKey ] ) && is_array( $_POST[ $answerListKey ] ) ) {
				// Validate each of the answers actually contain something, removing them if not.
				$answersToCheck = $_POST[ $answerListKey ];
				foreach ( $answersToCheck as $idx => $answer ) {
					// 2013-06-10 - Changed from (!trim($answer)) to if (strlen(trim($answer)) == 0) { to allow for
					// answers that are literally the number '0'.
					if ( strlen( trim( $answer ) ) == 0 ) {
						// Do nothing
					} // Clean up each answer if slashes used for escape characters.
					else {
						$qAns[ $idx ] = array( 'answer' => stripslashes( $answer ) );
					}
				} // end foreach
			} // end if answers are in an array
			// How many items are there in the list? None? Then make it false.
			if ( count( $qAns ) == 0 ) {
				return false;
			} // Got answers, so see if there are any matching images for these answers.
			else {
				// Detected some images to check to see if they're valid.
				if ( isset( $_POST[ $answerImageListKey ] ) && is_array( $_POST[ $answerImageListKey ] ) ) {
					$imagesToCheck = $_POST[ $answerImageListKey ];

					// Only interested in images if we have an answer set up.
					foreach ( $qAns as $idx => $answerDetails ) {
						// See if there's an image for an answer we've validated.
						if ( isset( $imagesToCheck[ $idx ] ) ) {
							// Yep, now just check it's sound and safe.
							$ansImage = trim( substr( strip_tags( $imagesToCheck[ $idx ] ), 0, 300 ) );
							if ( $ansImage ) {
								// All is sound, so store the image.
								$qAns[ $idx ]['image'] = $ansImage;
							}
						} // end if (isset($imagesToCheck[$idx]))
					}
				} // end if
			} // end else.

			return $qAns;
		}

		/**
		 * Extract the correct answer for a Multiple Choice question, using the specified answer key to check $_POST.
		 *
		 * @param String $correctAnswerKey The key to use to extract a correct answer.
		 * @param Array The list of questions to check that the correct answer falls into.
		 *
		 * @return String The correct answer, if it was found.
		 */
		public static function editSave_extractCorrectAnswer( $qAns, $correctAnswerKey ) {
			/*
			 * To store the  correct answers into the array of correct answers.
			 * @var array
			 */
			$qAnsCor = array();
			if ( isset( $_POST[ $correctAnswerKey ] ) ) {
				foreach ( $_POST[ $correctAnswerKey ] as $key => $value ) {
					// ### See if we have a correct answer, and it matches one of the items in the list.
					if ( isset( $_POST[ $correctAnswerKey ] ) && preg_match( '/^([0-9]+)$/', $value, $matches ) ) {
						$qAnsCor[] = $matches[1];
					}
				}
			}

			//No correct answer if no answers is specified.
			if ( count( $qAnsCor ) <= 0 ) {
				$qAnsCor = false;
			}
			// No correct answer if no answers, or specified answer is not in list of potential
			// answers.
			// if (empty($qAnsCor) || !$qAns || !isset($qAns[$qAnsCor])) {
			//     $qAnsCor = false;
			// }
			return $qAnsCor;
		}

		/**
		 * Clean the answer data and return it to the user. Check for an answer that looks like ans_%d.
		 *
		 * @param String $rawData The data that's being cleaned.
		 *
		 * @return String The cleaned data (just the index of the answer).
		 */
		public static function sanitizeAnswerData( $rawData ) {
			if ( preg_match( '/^ans_(\d+)$/', $rawData, $matches_a ) ) {
				return $matches_a[1];
			}

			return false;
		}

		/**
		 * Shows the field where the instructor can determine if answers are randomly presented
		 * to the user on the page.
		 *
		 * @param Integer $columnCount The number of columns that are being rendered to sh
		 *
		 * @return String The HTML for rendering the randomize answers field.
		 */
		protected function getSection_showRandomizeAnswersField( $columnCount ) {
			$html = '<tr>';
			$html .= sprintf(
				'<th>%s<span class="wpcw_inner_hint">%s</span></th>', __( 'Randomize Answers?', 'wp-courseware' ), __( '(Optional)', 'wp-courseware' )
			);

			$html .= '<td class="wpcw_quiz_details_randomize_answers">';

			// The checkbox to enable the feature
			$html .= sprintf(
				'<input name="question_multi_random_enable_%s" class="wpcw_quiz_details_enable" type="checkbox" %s />', $this->quizItem->question_id, ( $this->quizItem->question_multi_random_enable > 0 ? 'checked="checked"' : '' ), __( 'Yes, randomize the order of these answers.', 'wp-courseware' )
			);

			// The count of the items that will be randomized. Always include, but hide if not enabled.
			$html .= sprintf(
				'<span class="wpcw_quiz_details_count_wrap" %s>
									 <label>%s</label>
									 <input name="question_multi_random_count_%s" class="wpcw_quiz_details_count" type="text" value="%s" size="10" maxlength="10" />
									 <span class="wpcw_quiz_details_count_doc">%s</span>
								 </span>',
				// Hide if not enabled
				( $this->quizItem->question_multi_random_enable ? '' : 'style="display: none;"' ), __( 'Number of answers to display:', 'wp-courseware' ), $this->quizItem->question_id, $this->quizItem->question_multi_random_count, __( 'The correct answers will always appear in the selection of answers.', 'wp-courseware' )
			);

			$html .= '</td>';

			// Works out the space after the text area.
			$columnCount -= 2;
			if ( $columnCount > 0 ) {
				$html .= sprintf( '<td colspan="%d">&nbsp;</td>', $columnCount );
			}

			$html .= '</tr>';

			return $html;
		}
	}
}
