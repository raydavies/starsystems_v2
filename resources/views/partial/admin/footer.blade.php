<div class="footer text-left">
    <div class="container">
        <div class="row clearfix text-center">
            <p class="col-sm-12"><a href="mailto:jordanmand@gmail.com">Click here to email Jordan if something is broken</a></p>
        </div>

        <div id="form_wrapper" class="row hidden">
            <div class="col-md-6 col-md-offset-3 col-xs-12">
                <form method="post" action="" id="contact_form" novalidate="novalidate">
                    @csrf
                    <div class="form-group has-feedback">
                        <label for="subject" class="control-label">Subject</label>
                        <div class="control-input">
                            <input type="text" required name="subject" value="Something Is Broken!" id="subject" class="form-control" aria-describedby="subject-status">
                            <i class="fa form-control-feedback hidden" aria-hidden="true"></i>
                            <span id="subject_status" class="sr-only hidden"></span>
                        </div>
                        <div class="control-message">
                            <span class="errormsg">{{ $errors->first('subject') }}</span>
                        </div>
                    </div>

                    <div class="form-group has-feedback">
                        <label for="message" class="control-label">Message</label>
                        <div class="control-input">
                            <textarea required name="message" id="message" class="form-control" aria-describedby="message_status" cols="50" rows="10"></textarea>
                            <i class="fa form-control-feedback hidden" aria-hidden="true"></i>
                            <span id="message_status" class="sr-only hidden"></span>
                        </div>
                        <div class="control-message">
                            <span class="errormsg">{{ $errors->first('message') }}</span>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-success btn-lg">Send</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
