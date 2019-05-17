var _gaq=_gaq||[];_gaq.push(["_setAccount","UA-44796661-1"]),_gaq.push(["_setDomainName","starlearningsystems.com"]),_gaq.push(["_trackPageview"]),function(){var t=document.createElement("script");t.type="text/javascript",t.async=!0,t.src=("https:"==document.location.protocol?"https://ssl":"http://www")+".google-analytics.com/ga.js";var e=document.getElementsByTagName("script")[0];e.parentNode.insertBefore(t,e)}(),$(document).ready(function(){$(document).on("show.bs.collapse","#faq .question",function(){$(this).find(".caret").addClass("active")}),$(document).on("hide.bs.collapse","#faq .question",function(){$(this).find(".caret").removeClass("active")})});var FormManager=function(t,e){var a=this,s=(e=e||{first_name:"validName",last_name:"validName",email:"validEmail",subject:"validAlphaNum",message:"validInput"},{"email:validEmail":"The email field must contain a valid email address"}),n={};this.init=function(){t.data("validateOnStart")&&a.validateForm(),t.on("change",".form-control",function(){a.validateInput($(this))}),t.on("submit",a.validateForm)},this.validateForm=function(){var e=!0;return t.find(".form-group").each(function(){a.validateInput($(this).find(".form-control"))||(e=!1)}),e},this.validateInput=function(t){var i,o,r,c,l=t.attr("name");return n[l]="",i=e[l]?e[l]:"validInput",a[i](l,t.val())?(a.setSuccessStatus(t),o=!0):(n[l]&&(c=n[l],s[l+":"+c]&&(r=s[l+":"+c])),a.setErrorStatus(t,r),o=!1),o},this.setSuccessStatus=function(t){t.siblings(".form-control-feedback").removeClass("fa-close hidden").addClass("fa-check"),t.siblings("span.sr-only").removeClass("hidden").text("(success)"),t.closest(".form-group").removeClass("has-error").addClass("has-success").find(".errormsg").empty()},this.setErrorStatus=function(t,e){var a=t.attr("name").replace(/[-_]+/," ");e=e||"The "+a+" field is required";t.siblings(".form-control-feedback").removeClass("fa-check hidden").addClass("fa-close"),t.siblings("span.sr-only").removeClass("hidden").text("(error)"),t.closest(".form-group").removeClass("has-success").addClass("has-error").find(".errormsg").text(e)},this.validInput=function(t,e){return""!=a.trimSpace(e)},this.validName=function(t,e){if(a.validInput(t,e)){if(e.length<=255)return!0;n[t]="validName"}return!1},this.validAlphaNum=function(t,e){if(a.validInput(t,e)){if(e.match(/^[a-zA-Z0-9\-\_ ]+$/))return!0;n[t]="validAlphaNum"}return!1},this.validEmail=function(t,e){if(a.validInput(t,e)){if(e.match(/^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/))return!0;n[t]="validEmail"}return!1},this.validStateAbbr=function(t,e){if(a.validInput(t,e)){if(e.match(/^[a-zA-Z]{2}$/))return!0;n[t]="validStateAbbr"}return!1},this.noValidate=function(t,e){return!0},this.trimSpace=function(t){return t.replace(/^\s+|\s+$/g,"")}};function LessonPicker(){var t=this;this.init=function(){$("#lesson_select_form").on("change.level","#level_select",function(){var e=parseInt($(this).val(),10);t.loadSubjects(e)})},this.loadSubjects=function(t){$.ajax({url:"/lesson-topics/"+t,dataType:"json",success:function(t){var e,a;if(t.subjects.length)for(e in $("#subject_select").empty(),t.subjects)a=$("<option/>").text(t.subjects[e].name).val(t.subjects[e].id),$("#subject_select").append(a)}})}}function showScrollButton(){var t=$("body").height()/4;$(document).scrollTop()>=t?$(".scroll-btn").removeClass("hidden"):$(".scroll-btn").addClass("hidden")}function scrollToTop(t){t=parseInt(t,10);$("html, body").animate({scrollTop:0},t)}$(document).ready(function(){(new LessonPicker).init()}),$(document).ready(function(){$(window).bind("scroll",function(){showScrollButton()}),$("a[href=#top]").bind("click",function(t){t.preventDefault(),scrollToTop(1e3)}),showScrollButton()}),$(document).ready(function(){$(".carousel-inner").swipe({swipeLeft:function(t,e,a,s,n){$(this).parent().carousel("next")},swipeRight:function(){$(this).parent().carousel("prev")},threshold:50})});
