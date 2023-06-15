const Log = require("laravel-mix/src/Log");

$(function() {
    if (error.length > 0) {
        activeExpired(error[0]['message']);
        if (error[0]['statusCode'] == 408) {
            $("#alert_confirm").click(function() {
                window.close();
            })
        }
    }

    /* Handel page expired */
    function activeExpired(text) {
        modalObj.show();
        $("#alert_content").text(text);
    }

    /* Handel api errors. */
    function activeAlert(messageArray) {
        let html = '';
        $.each(messageArray, function(type, errors) {
            let inside = outside = '';
            $.each(errors, function(key, msg) {
                inside += `<li>${msg}</li>`;
            })
            outside = `<li>${type}: 
                            <ul>${inside}</ul>
                        </li>`;
            // html += `${outside}`;
            html += inside;
        });
        html = `<ul>${html}</ul>`;
        $("#alert_content").html(html).removeClass('text-center');
        modalObj.show();
    }

    /* Reset alert area. */
    $("#alert_confirm").click(function() {
        modalObj.hide();
        $(".loading").addClass('d-none');
        $("#alert_content").addClass('text-center');
        $("#submitBtn, #otpBtn").removeClass('disabled');
        $("#submitBtn_text, #otpBtn_text").removeClass('d-none');
    })

    /* Get verify code. */
    $("#registerForm").submit(function(event) {
        event.preventDefault();
        $("#submitBtn").addClass('disabled');
        $(".loading").removeClass('d-none');
        $("#submitBtn_text").addClass('d-none');
        let postData = $("#registerForm").serializeArray();
        $.ajax({
            type: "POST",
            url: formUrl,
            data: postData,
            headers: {
                'Accept': 'Application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            success: function(data) {
                console.log(data);
                if (verifyType == 'otp') {
                    $("#enterOTPArea").removeClass('d-none');
                    $("#getOPTArea, .loading").addClass('d-none');
                    countDown(10, $('#countDown'));
                } else {
                    $("#alert_content").text(data.message);
                    $("#alert_confirm").addClass('d-none');
                    modalObj.show();
                    setTimeout(() => {
                        location.replace(data.redirect_url);
                    }, 1000);
                }
            },
            error: function(data) {
                let response = data.responseJSON;
                console.log(response);
                if (data.status == 408) {
                    activeExpired(response.message);
                    $("#alert_confirm").click(function() {
                        window.close();
                        location.reload();
                    })
                } else if (data.status == 400) {
                    /* 帳密錯誤 */
                    activeExpired(response.message);
                    $("#alert_confirm").click(function() {
                        modalObj.hide();
                    })
                } else if (data.status == 500) {
                    /* 建立token失敗 */
                    activeExpired(response.message);
                    $("#alert_confirm").click(function() {
                        modalObj.hide();
                    })
                } else {
                    activeAlert(response.errors);
                }
            }
        })
    })

    /* Resend */
    $("#resend_sms").click(function() {
        $("#submitBtn_text, #getOPTArea, #countDown").removeClass('d-none');
        $("#enterOTPArea, .loading").addClass('d-none');
        $("#submitBtn, #otpBtn").removeClass('disabled');
        $("#resend_sms").removeClass('act-show');
        $(`input[name='${primaryKey}'], [name='otp']`).val('');
    })

    /* Confirm OTP code. */
    $("#otpForm").submit(function(event) {
        event.preventDefault();
        $("#otpBtn").addClass('disabled');
        $(".loading").removeClass('d-none');
        $("#otpBtn_text").addClass('d-none');
        let data = $("#registerForm").serializeArray(),
            otpCode = $("input[name='otp']").val(),
            otpData = {
                'name': 'otp',
                'value': otpCode
            };
        data.push(otpData);
        $.ajax({
            type: "POST",
            url: verifyOtpUrl,
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            success: function(response) {
                console.log(response);
                $("#alert_content").text(response.message);
                $("#alert_confirm").addClass('d-none');
                modalObj.show();
                setTimeout(() => {
                    location.replace(response.redirect_url);
                }, 1000);
            },
            error: function(data) {
                let response = data.responseJSON;
                console.log(response);
                activeAlert(response.errors);
            }
        })
    })

    /* Count down. */
    function countDown(seconds, element) {
        function updateCountdown() {
            let minutes = Math.floor(seconds / 60),
                remainingSeconds = seconds % 60,
                formattedTime = minutes + ':' + remainingSeconds;
            element.text(formattedTime);
            seconds--;
            if (seconds < 0) {
                clearInterval(countdownInterval);
                element.addClass('d-none');
                $("#resend_sms").addClass('act-show');
            }
        }
        updateCountdown();
        var countdownInterval = setInterval(updateCountdown, 1000);
    }

    /* Disable enter attribute. */
    $('#registerForm, #otpForm').on('keyup keypress', function(e) {
        let keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    })

    /* Disable a tag drag. */
    let links = $('a');
    for (var i = 0; i < links.length; i++) {
        links[i].ondragstart = function() {
            return false;
        };
    }

    /* Reminder about leaving page. */
    // $(window).on('beforeunload', function(event) {
    //     return 'Are you sure to leave?';
    // })
});