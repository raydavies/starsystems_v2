(()=>{var e;(e=e||[]).push(["_setAccount","UA-44796661-1"]),e.push(["_setDomainName","starlearningsystems.com"]),e.push(["_trackPageview"]),function(){var e=document.createElement("script");e.type="text/javascript",e.async=!0,e.src=("https:"==document.location.protocol?"https://ssl":"http://www")+".google-analytics.com/ga.js";var t=document.getElementsByTagName("script")[0];t.parentNode.insertBefore(e,t)}(),$(document).ready((function(){$(document).on("show.bs.collapse","#faq .question",(function(){$(this).find(".caret").addClass("active")})),$(document).on("hide.bs.collapse","#faq .question",(function(){$(this).find(".caret").removeClass("active")}))})),(()=>{"use strict"})(),(()=>{function e(){var e=this;this.init=function(){$("#lesson_select_form").on("change.level","#level_select",(function(){var t=parseInt($(this).val(),10);e.loadSubjects(t)}))},this.loadSubjects=function(e){$.ajax({url:"/lesson-topics/"+e,dataType:"json",beforeSend:function(){$("#subject_select").prop("disabled",!0)},success:function(e){var t,n;if(e.subjects.length)for(t in $("#subject_select").prop("disabled",!1).empty(),e.subjects)n=$("<option/>").text(e.subjects[t].name).val(e.subjects[t].id),$("#subject_select").append(n)}})}}$(document).ready((function(){(new e).init()}))})(),(()=>{function e(){var e=$("body").height()/4;$(document).scrollTop()>=e?$(".scroll-btn").removeClass("hidden"):$(".scroll-btn").addClass("hidden")}$(document).ready((function(){$(window).bind("scroll",(function(){e()})),$("a[href=#top]").bind("click",(function(e){e.preventDefault(),function(e){e=parseInt(e,10);$("html, body").animate({scrollTop:0},e)}(1e3)})),e()}))})(),$(document).ready((function(){$(".carousel-inner").swipe({swipeLeft:function(e,t,n,s,o){$(this).parent().carousel("next")},swipeRight:function(){$(this).parent().carousel("prev")},threshold:50})}))})();