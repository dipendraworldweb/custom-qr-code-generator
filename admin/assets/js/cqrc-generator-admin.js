"use strict";

//Validate URL field.
function validateInputsUrl() {
    var isValid = true;
    // Validate URL field
    var url = jQuery('#qrcode_url').val();
    if (url.length > 75) {
        jQuery('#url_error').show();
        isValid = false;
    } else {
        jQuery('#url_error').hide();
    }
}

//Validate Name field.
function validateInputsName() {
    var isValid = true;
    var name = jQuery('#qrcode_name').val();
    var nameRegex = /^[A-Za-z\s]+$/;
    if (name.length > 30 || !nameRegex.test(name)) {
        jQuery('#name_error').show();
        isValid = false;
    } else {
        jQuery('#name_error').hide();
    }

            // Disable or enable button based on validation
    jQuery('#wwt-qrcode-generate-form p.submit input#submit').prop('disabled', !isValid);
}

// Function to validate the URL
function validateUrl() {
   var urlInput = jQuery('#qrcode_url');
   var urlValue = urlInput.val();
   var urlError = jQuery('#url_error');
   urlError.hide();
   try {
    new URL(urlValue);
} catch (_) {
    urlError.text('Please enter a valid URL.').show();
    return false;
}
return true;
}

// Function to validate the Logo
function toggleLogoFields() {
    var $customLogoOption = jQuery('#custom_logo_option');
    var $uploadLogoOption = jQuery('#upload_logo_option');
    var $defaultLogoSelect = jQuery('#default_logo');
    var $uploadLogoInput = jQuery('#upload_logo_button');
    var $logoPreview = jQuery('#logo_preview');
    var $logoPreviews = jQuery('#logo_previews');

    if ($customLogoOption.is(':checked')) {
        $defaultLogoSelect.show();
        $uploadLogoInput.hide();
        $logoPreview.show();

        if ($logoPreviews.length) {
            $logoPreviews.hide();
        }
    } else if ($uploadLogoOption.is(':checked')) {
        $defaultLogoSelect.hide();
        $uploadLogoInput.show();
        $logoPreview.hide();

        if ($logoPreviews.length) {
            $logoPreviews.show();
        }
    }

    if ($defaultLogoSelect.val() == 'default') {
        $logoPreview.hide();
    }
}

// Function to Preview the Logo
function updateLogoPreview() {
    var selectedLogo = jQuery('#default_logo').val();
    var $imgPreview = jQuery('#logo_preview');
    var pluginLogoImagePath = wwtQrCodeGenerator.pluginLogoImagePath;

    var logoMap = {
        'default': '',
        'instagram-circle': 'instagram-circle.png',
        'facebook': 'facebook.png',
        'youtube-circle': 'youtube-circle.png',
        'whatsapp-circle': 'whatsapp-circle.png',
        'linkedin-circle': 'linkedin-circle.png',
        'twitter-circle': 'twitter-circle.png',
        'gmail': 'gmail.png',
        'google-play': 'google-play.png',
        'googleplus-circle': 'googleplus-circle.png',
        'xing-circle': 'xing-circle.png',
        'google-calendar': 'google-calendar.png',
        'google-forms': 'google-forms.png',
        'google-maps': 'google-maps.png',
        'google-meet': 'google-meet.png',
        'google-sheets': 'google-sheets.png',
        'hangouts-meet': 'hangouts-meet.png',
        'spotify': 'spotify.png',
        'telegram': 'telegram.png'
    };

    var logoFileName = logoMap[selectedLogo] || '';
    $imgPreview.attr('src', pluginLogoImagePath + logoFileName).toggle(logoFileName !== '');
}

// Function to validate the Template
function updateTemplatePreview() {
    var selectedTemplate = jQuery('#template_name').val();
    var $templatePreview = jQuery('#template_preview');
    var pluginTemplateImagePath = wwtQrCodeGenerator.pluginTemplateImagePath;

    var templateMap = {
        'default': 'default-1.png',
        'facebook': 'facebook.png',
        'youtube-circle': 'youtube.png',
        'twitter-circle': 'twitter.png',
        'instagram-circle': 'instagram.png',
        'whatsapp-circle': 'whatsapp.png',
        'gmail': 'gmail.png',
        'linkedin-circle': 'linkedin.png'
    };

    var templateFileName = templateMap[selectedTemplate] || '';
    $templatePreview.attr('src', pluginTemplateImagePath + templateFileName).toggle(templateFileName !== '');
}

// Function to validate the QR Frames
function updateFramePreview() {
    var selectedFrame = jQuery('#default_frame').val();
    var $framePreview = jQuery('#frame_preview');
    var pluginFrameImagePath = wwtQrCodeGenerator.pluginFrameImagePath;

    var frameMap = {
        'default': 'default-1.png',
        'balloon-bottom': 'balloon-bottom.png',
        'balloon-bottom-1': 'balloon-bottom-1.png',
        'balloon-top': 'balloon-top.png',
        'balloon-top-2': 'balloon-top-2.png',
        'banner-bottom': 'banner-bottom.png',
        'banner-bottom-3': 'banner-bottom-3.png',
        'banner-top': 'banner-top.png',
        'banner-top-4': 'banner-top-4.png',
        'box-bottom': 'box-bottom.png',
        'box-bottom-5': 'box-bottom-5.png',
        'box-top': 'box-top.png',
        'box-top-6': 'box-top-6.png',
        'focus-8-lite': 'focus-8-lite.png',
        'focus-lite': 'focus-lite.png'
    };

    var frameFileName = frameMap[selectedFrame] || '';
    $framePreview.attr('src', pluginFrameImagePath + frameFileName).toggle(frameFileName !== '');
}

// Function to validate the Eye Frames
function updateEyeFramePreview() {
    var selectedEyeFrame = jQuery('#eye_frame_name').val();
    var $eyeFramePreview = jQuery('#eye_frame_preview');
    var pluginEyeFrameImagePath = wwtQrCodeGenerator.pluginEyeFrameImagePath;

    var eyeFrameMap = {
        'default': '',
        'frame0': 'frame0.png',
        'frame1': 'frame1.png',
        'frame2': 'frame2.png',
        'frame3': 'frame3.png',
        'frame4': 'frame4.png',
        'frame5': 'frame5.png',
        'frame6': 'frame6.png',
        'frame7': 'frame7.png',
        'frame8': 'frame8.png',
        'frame9': 'frame9.png',
        'frame10': 'frame10.png',
        'frame11': 'frame11.png',
        'frame12': 'frame12.png',
        'frame13': 'frame13.png',
        'frame14': 'frame14.png'
    };

    var eyeFrameFileName = eyeFrameMap[selectedEyeFrame] || 'frame0.png';
    $eyeFramePreview.attr('src', pluginEyeFrameImagePath + eyeFrameFileName).toggle(selectedEyeFrame !== 'default');
}

// Function to validate the Eye Balls
function updateEyeBallsPreview() {
    var selectedEyeBall = jQuery('#eye_balls_name').val();
    var $eyeBallsPreview = jQuery('#eye_balls_preview');
    var pluginEyeBallsImagePath = wwtQrCodeGenerator.pluginEyeBallsImagePath;

    var eyeBallsMap = {
        'default': '',
        'ball0': 'ball0.png',
        'ball1': 'ball1.png',
        'ball2': 'ball2.png',
        'ball3': 'ball3.png',
        'ball4': 'ball4.png',
        'ball5': 'ball5.png',
        'ball6': 'ball6.png',
        'ball7': 'ball7.png',
        'ball8': 'ball8.png',
        'ball9': 'ball9.png',
        'ball10': 'ball10.png',
        'ball11': 'ball11.png',
        'ball12': 'ball12.png',
        'ball13': 'ball13.png',
        'ball14': 'ball14.png',
        'ball15': 'ball15.png',
        'ball16': 'ball16.png',
        'ball17': 'ball17.png',
        'ball18': 'ball18.png',
        'ball19': 'ball19.png',
        'ball20': 'ball20.png',
        'ball21': 'ball21.png'
    };

    var eyeBallsFileName = eyeBallsMap[selectedEyeBall] || 'ball0.png';
    $eyeBallsPreview.attr('src', pluginEyeBallsImagePath + eyeBallsFileName).toggle(selectedEyeBall !== 'default');
}

// Function to limit.
function debounce(func, wait) {
    var timeout;
    return function() {
        var context = this, args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            func.apply(context, args);
        }, wait);
    };
}

// Function to QR Template Preview
function WwtPreviousQrcodeTemplate() {
    var loader = jQuery('#qrcode-loader');
    var qrcode_name = jQuery('#qrcode_name').val();
    var logo_option = jQuery('input[name="logo_option"]:checked').val();
    var url = jQuery('#qrcode_url').val();
    var template_name = jQuery('#template_name').val();
    var qrid = jQuery('input[name="qrid"]').val();
    var upload_logo_url = jQuery('input[name="upload_logo_url"]').val();
    var qrcode_level = jQuery('select[name="qrcode_level"]').val();
    var default_logo = jQuery('#default_logo').val();
    var default_frame = jQuery('#default_frame').val();
    var eye_frame_name = jQuery('#eye_frame_name').val();
    var eye_balls_name = jQuery('#eye_balls_name').val();
    var qr_code_color = jQuery('.qr_color_picker_1').val();
    var qr_eye_frame_color = jQuery('.qr_color_picker_2').val();
    var qr_eye_color = jQuery('.qr_color_picker_3').val();
    
    // if url value is empty then return true.
    if (url == '') {
        return true;
    }
    var nonce = wwtQrCodeGenerator.nonce;
    if (template_name.length > 0) {
        loader.show();
        jQuery.ajax({
            url: wwtQrCodeGenerator.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'cqrc_handle_qrurl_insert_record',
                qrcode_name: qrcode_name,
                qrcode_url: url,
                template_name: template_name,
                qrid: qrid,
                upload_logo_url: upload_logo_url,
                logo_option: logo_option,
                default_logo: default_logo,
                default_frame: default_frame,
                eye_frame_name: eye_frame_name,
                eye_balls_name: eye_balls_name,
                qr_code_color: qr_code_color,
                qr_eye_color: qr_eye_color,
                qr_eye_frame_color: qr_eye_frame_color,
                qrcode_level: qrcode_level,
                _ajax_nonce: nonce
            },
            success: function(response) {
                if (response.success) {
                    loader.hide();
                    jQuery('#qrcode_image').show();
                    jQuery('#qrcode_default').hide();
                    
                    // Add a cache-busting query parameter to the URL
                    var uniqueUrl = response.data.url_data + '?t=' + new Date().getTime();
                    jQuery('#qrcode_image').attr('src', uniqueUrl);
                } else {
                    loader.hide();
                }
            },
            error: function() {
                loader.hide();
            }
        });
    }
}

// Function to get the download URL
function getDownloadUrl(id, type) {
    return `${wwtQrCodeGenerator.downloadUrl}?action=download_qr&id=${id}&type=${type}&custom=custom_popup`;
}

// Function to remove the popup
function closePopup() {
    jQuery('#download-popup, #download-popup-overlay').remove();
}

function countWords(str) {
    return str.trim().split(/\s+/).length;
}

// Function to validate the Password Field
function validatePassword(password) {
    // Reset colors and icons
    jQuery('#password-error-uppercase, #password-error-lowercase, #password-error-digit, #password-error-special').css('color', 'red');
    jQuery('#icon-uppercase, #icon-lowercase, #icon-digit, #icon-special').removeClass('fa-check').addClass('fa-times').css('color', 'red');

    var hasErrors = false;
    if (password) {
        if (password.length > 10) {
            hasErrors = true;
            jQuery('#password-error').html("Password must be at most 10 characters.").css('color', 'red').show();
        } else {
            jQuery('#password-error').hide();
        }

        if (!/[A-Z]/.test(password)) {
            hasErrors = true;
            jQuery('#icon-uppercase').removeClass('fa-check').addClass('fa-times').css('color', 'red');
        } else {
            jQuery('#icon-uppercase').removeClass('fa-times').addClass('fa-check').css('color', 'green');
            jQuery('#password-error-uppercase').css('color', 'green');
        }

        if (!/[a-z]/.test(password)) {
            hasErrors = true;
            jQuery('#icon-lowercase').removeClass('fa-check').addClass('fa-times').css('color', 'red');
        } else {
            jQuery('#icon-lowercase').removeClass('fa-times').addClass('fa-check').css('color', 'green');
            jQuery('#password-error-lowercase').css('color', 'green');
        }

        if (!/[0-9]/.test(password)) {
            hasErrors = true;
            jQuery('#icon-digit').removeClass('fa-check').addClass('fa-times').css('color', 'red');
        } else {
            jQuery('#icon-digit').removeClass('fa-times').addClass('fa-check').css('color', 'green');
            jQuery('#password-error-digit').css('color', 'green');
        }

        if (!/[!@#$%^&*(),.?":{}|<>/';\[\]~\-+_]/.test(password)) {
            hasErrors = true;
            jQuery('#icon-special').removeClass('fa-check').addClass('fa-times').css('color', 'red');
        } else {
            jQuery('#icon-special').removeClass('fa-times').addClass('fa-check').css('color', 'green');
            jQuery('#password-error-special').css('color', 'green');
        }
    }

    return hasErrors;
}

// Function to Disable for Required Field.
function toggleButtonState(isDisabled) {
    jQuery('.form-buttons p.submit input#submit').prop('disabled', isDisabled);
}

// Function to Disable the Template Option.
function toggleTemplateSelect() {
    var urlValue = jQuery('#qrcode_url').val();
    
    // Ensure urlValue is a string before calling .trim(), and check if it's empty
    if (String(urlValue).trim() === '') {
        jQuery('#template_name').prop('disabled', true);
    } else {
        jQuery('#template_name').prop('disabled', false);
    }
}

// Use jQuery to select checkboxes and set the checked status
function toggleCheckboxes(selectAllCheckbox) {
    jQuery('input[name="columns[]"]').each(function() {
        if (!jQuery(this).prop('disabled')) {
            jQuery(this).prop('checked', jQuery(selectAllCheckbox).prop('checked'));
        }
    });
}

// Use jQuery to check if all checkboxes are checked
function updateSelectAll() {
    const allChecked = jQuery('input[name="columns[]"]:not(:disabled)').length === jQuery('input[name="columns[]"]:not(:disabled):checked').length;
    jQuery('#select-all').prop('checked', allChecked);
}
function copyToClipboard(e) {
  if (navigator.clipboard && window.isSecureContext) {
    navigator.clipboard.writeText(e).then(function() {
      alert("Shortcode copied to clipboard!");
  }).catch(function(err) {
      alert("Error copying text: " + err);
  });
} else {
    // Create a temporary textarea element using jQuery
    var $temp = jQuery("<textarea>");
    jQuery("body").append($temp);
    $temp.val(e).select(); // Set the value and select it

    try {
      document.execCommand("copy"); // Try to copy the selected text
      alert("Shortcode copied to clipboard!");
  } catch (err) {
      alert("Error copying text: " + err);
  }

    // Remove the temporary textarea after copying
  $temp.remove();
}
}

jQuery(document).ready(function($) {
    var imgPreviews = jQuery('#default_logo').val();
    var eye_frame_name = jQuery('#eye_frame_name').val();
    var eye_balls_name = jQuery('#eye_balls_name').val();
    var default_frame = jQuery('#default_frame').val();
    var logo_preview = jQuery('#logo_preview');
    var frame_preview = jQuery('#frame_preview');
    var eye_frame_preview = jQuery('#eye_frame_preview');
    var eye_balls_preview = jQuery('#eye_balls_preview');

    if (imgPreviews == 'default') {
     logo_preview.hide();
 }
 if (eye_frame_name == 'default') {
     eye_frame_preview.hide();
 }   
 if (eye_balls_name == 'default') {
     eye_balls_preview.hide();
 }

 jQuery('#template_name').on('change', function() {

    var value = jQuery(this).val();
    var settings = {};

    switch (value) {
    case 'facebook':
        settings = {
            default_logo: 'facebook',
            default_frame: 'default',
            eye_frame_name: 'frame14',
            eye_balls_name: 'ball16',
            qr_code_color: '#2c4270',
            qr_eye_color: '#2c4270',
            qr_eye_frame_color: '#2c4270',
            qrcode_level: 'QR_ECLEVEL_M'
        };
        break;
    case 'youtube-circle':
        settings = {
            default_logo: 'youtube-circle',
            default_frame: 'default',
            eye_frame_name: 'frame13',
            eye_balls_name: 'ball14',
            qr_code_color: '#BF2626',
            qr_eye_color: '#EE0F0F',
            qr_eye_frame_color: '#EE0F0F',
            qrcode_level: 'QR_ECLEVEL_Q'
        };
        break;
    case 'twitter-circle':
        settings = {
            default_logo: 'twitter-circle',
            default_frame: 'default',
            eye_frame_name: 'frame5',
            eye_balls_name: 'ball11',
            qr_code_color: '#55ACEE',
            qr_eye_color: '#55ACEE',
            qr_eye_frame_color: '#55ACEE',
            qrcode_level: 'QR_ECLEVEL_Q'
        };
        break;
    case 'instagram-circle':
        settings = {
            default_logo: 'instagram-circle',
            default_frame: 'default',
            eye_frame_name: 'frame5',
            eye_balls_name: 'ball4',
            qr_code_color: '#0d1766',
            qr_eye_color: '#0d1766',
            qr_eye_frame_color: '#8224e3',
            qrcode_level: 'QR_ECLEVEL_H'
        };
        break;
    case 'whatsapp-circle':
        settings = {
            default_logo: 'whatsapp-circle',
            default_frame: 'default',
            eye_frame_name: 'frame2',
            eye_balls_name: 'ball2',
            qr_code_color: '#2ebd38',
            qr_eye_color: '#2ebd38',
            qr_eye_frame_color: '#2ebd38',
            qrcode_level: 'QR_ECLEVEL_M'
        };
        break;
    case 'gmail':
        settings = {
            default_logo: 'gmail',
            default_frame: 'default',
            eye_frame_name: 'frame14',
            eye_balls_name: 'ball14',
            qr_code_color: '#e4594c',
            qr_eye_color: '#e4594c',
            qr_eye_frame_color: '#e4594c',
            qrcode_level: 'QR_ECLEVEL_Q'
        };
        break;
    case 'linkedin-circle':
        settings = {
            default_logo: 'linkedin-circle',
            default_frame: 'default',
            eye_frame_name: 'frame0',
            eye_balls_name: 'ball0',
            qr_code_color: '#005881',
            qr_eye_color: '#005881',
            qr_eye_frame_color: '#005881',
            qrcode_level: 'QR_ECLEVEL_M'
        };
        break;
    case 'default':
        settings = {
            default_logo: 'default',
            default_frame: 'default',
            eye_frame_name: 'default',
            eye_balls_name: 'default',
            qr_code_color: '#000000',
            qr_eye_color: '#000000',
            qr_eye_frame_color: '#000000',
            qrcode_level: 'QR_ECLEVEL_M'
        };
        break;
    }

        // Apply settings
    $.each(settings, function(key, value) {
        var element = jQuery(`select[name="${key}"], input[name="${key}"]`);
        element.val(value).trigger('change');
    });
});

    // Bind validation to focusout event on inputs
 jQuery('#qrcode_url').on('focusout', function() {
    if (jQuery('#qrcode_url').val() != '')  {
        validateInputsUrl();
    }
});

 jQuery('#qrcode_name').on('focusout', function() {
    if(jQuery('#qrcode_name').val() != '')  {
        validateInputsName();
    }
});

    // Event listener for real-time validation
 $('#qrcode_url').on('input', function() {
   validateUrl();
});

    // Bind the functions to the relevant events
 jQuery('#custom_logo_option, #upload_logo_option').change(toggleLogoFields);
 jQuery('#default_logo').change(updateLogoPreview);
 jQuery('#template_name').change(updateTemplatePreview);
 jQuery('#default_frame').change(updateFramePreview);
 jQuery('#eye_frame_name').change(updateEyeFramePreview);
 jQuery('#eye_balls_name').change(updateEyeBallsPreview);

    // Initialize visibility and preview
 toggleLogoFields();
 updateLogoPreview();
 updateTemplatePreview();
 updateFramePreview();
 updateEyeFramePreview();
 updateEyeBallsPreview();

 var loader = $('#qrcode-loader');
 $('#qrcode_default').show();

    // Debounced version of WwtPreviousQrcodeTemplate
 var debouncedWwtPreviousQrcodeTemplate = debounce(WwtPreviousQrcodeTemplate, 500);

 jQuery('input[name="qrcode_url"]').on('focusout', debouncedWwtPreviousQrcodeTemplate);
 jQuery('select[name="template_name"]').on('change', debouncedWwtPreviousQrcodeTemplate);
 jQuery('select[name="qrcode_level"]').on('change', debouncedWwtPreviousQrcodeTemplate);
 jQuery('#default_logo').on('change', debouncedWwtPreviousQrcodeTemplate);
 jQuery('#default_frame').on('change', debouncedWwtPreviousQrcodeTemplate);
 jQuery('#eye_frame_name').on('change', debouncedWwtPreviousQrcodeTemplate);
 jQuery('#eye_balls_name').on('change', debouncedWwtPreviousQrcodeTemplate);

 jQuery('.wp-color-picker').wpColorPicker({
    change: function(event, ui) {
        debouncedWwtPreviousQrcodeTemplate();
    }
});

 var mediaUploader;
 jQuery('#upload_logo_button').click(function(e) {
    e.preventDefault();

        // If the media uploader already exists, open it.
    if (mediaUploader) {
        mediaUploader.open();
        return;
    }

        // Create a new media uploader instance
    mediaUploader = wp.media({
        title: 'Select or Upload Logo',
        button: {
            text: 'Use this logo'
        },
        multiple: false
    });

        // Handle the media selection
    mediaUploader.on('select', function() {
        var attachment = mediaUploader.state().get('selection').first().toJSON();
        var fileSize = attachment.filesizeInBytes;
            var maxFileSize = 5242880; // 5MB in bytes
            var fileType = attachment.mime;

            var allowedMimeTypes = ['image/jpeg', 'image/png'];

            // Validate file type
            if (!allowedMimeTypes.includes(fileType)) {
                alert('Only JPG and PNG files are allowed.');
                return;
            }

            // Validate file size
            if (fileSize > maxFileSize) {
                alert('Maximum file size exceeded (5MB).');
                return;
            }

            // If validation passes, update the field and preview image
            jQuery('#upload_logo_url').val(attachment.url);
            jQuery('#logo_preview').attr('src', attachment.url).show();
            jQuery('#logo_previews').hide();

            // Call the WwtPreviousQrcodeTemplate() function
            WwtPreviousQrcodeTemplate();
        });

    mediaUploader.open();
});

    // Handle the click event on download links
 $(document).on('click', '.qrcode-download-link-trigger', function(e) {
    e.preventDefault();

    var $this = $(this);
    var qrId = $this.data('id');
    var qrName = $this.data('name');

        // Create the popup HTML
    var popupHtml = `
    <div id="download-popup-overlay"></div>
    <div id="download-popup">
    <button id="close-popup" class="button button-secondary">X</button>
    <h2>Download QR Code: ${qrName}</h2>
    <div class="download-buttons">
    <a class="button button-primary" href="${getDownloadUrl(qrId, 'png')}" download="qrcode.png">PNG</a>
    <a class="button button-primary" href="${getDownloadUrl(qrId, 'jpg')}" download="qrcode.jpg">JPG</a>
    <a class="button button-primary" href="${getDownloadUrl(qrId, 'pdf')}" download="qrcode.pdf">PDF</a>
    </div>
    </div>
    `;

        // Append the popup HTML to the body
    $('body').append(popupHtml);
        // Handle the close button click
    $(document).on('click', '#close-popup', function() {
        closePopup();
    });

        // Close popup when clicking outside the popup
    $(document).on('click', '#download-popup-overlay', function() {
        closePopup();
    });

        // Close popup when pressing the Escape key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            closePopup();
        }
    });
});

 var $toggleButton = $('#toggle-settings');
 var $settingsElements = $('.additional-settings');
 var isVisible = false;

 $toggleButton.on('click', function() {
        // Validate the input fields
    var url = $('#qrcode_url').val().trim();
    var name = $('#qrcode_name').val().trim();
    var description = $('#qrcode_description').val().trim();

        // Error message elements
    var $urlError = $('#url_error');
    var $nameError = $('#name_error');
    var $descriptionError = $('#description_error');

        // Clear previous error messages
    $urlError.hide();
    $nameError.hide();
    $descriptionError.hide();

        // Reset error texts
    $urlError.text('');
    $nameError.text('');
    $descriptionError.text('');

        var hasError = false; // Flag to track errors

        // Check for empty fields
        if (!url) {
            $urlError.text('Please fill up the required field!').show();
            hasError = true;
        }
        if (!name) {
            $nameError.text('Please fill up the required field!').show();
            hasError = true;
        }
        if (!description) {
            $descriptionError.text('Please fill up the required field!').show();
            hasError = true;
        }

        // If there are errors, stop execution
        if (hasError) {
            return;
        }

        // Toggle settings if all fields are filled
        isVisible = !isVisible;
        $settingsElements.each(function() {
            $(this).toggle(isVisible);
        });

        $toggleButton.text(isVisible ? 'Hide Additional Settings' : 'Show Additional Settings');
    });

 var maxWords = 100;
 var subbtn = $('.form-buttons p.submit input#submit');

 $('#qrcode_description').on('input', function() {
    var textareaValue = $(this).val();
    var wordCount = countWords(textareaValue);

    if (wordCount > maxWords) {
        $('#description_error').show();
        subbtn.prop('disabled', true);
    } else {
        $('#description_error').hide();
        subbtn.prop('disabled', false);
    }
});

 $('.site-show-hide-password-ed').on('click', function() {
    var passwordInput = $('#password');
    var passwordFieldType = passwordInput.attr('type');

    if (passwordFieldType === 'password') {
        passwordInput.attr('type', 'text');
        $(this).find('i').removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        passwordInput.attr('type', 'password');
        $(this).find('i').removeClass('fa-eye-slash').addClass('fa-eye');
    }
});
 validatePassword($('#password').val());
 $('#password').on('input', function() {
    var password = $(this).val();
    password = password.replace(/\s+/g, '');

    if (password.length > 10) {
        password = password.substring(0, 10);
    }

    $(this).val(password);
    var hasErrors = validatePassword(password);
    if (password.length === 0) {
        toggleButtonState(false);
    } else {
        toggleButtonState(hasErrors);
    }
});

 toggleTemplateSelect();
 $('#qrcode_url').on('input', function() {
    toggleTemplateSelect();
});

 $('#export-qrcode-report').on('click', function(event) {
    const checkboxes = $('input[name="columns[]"]');
    const errorMessageDiv = $('#error-message');
    const anyChecked = checkboxes.is(':checked');

        // If no checkbox is checked, show an error message
    if (!anyChecked) {
        event.preventDefault();
        errorMessageDiv.text('Please select at least one column to export.');
        errorMessageDiv.show();
    } else {
        errorMessageDiv.hide();
    }
});
 $("#copy-code-icon").on("click", function () {
   var e = $("#shortcode-code").text(),
   o = $("<textarea>").val(e).appendTo("body");
   o.select(), document.execCommand("copy"), o.remove(), $("#copy-message").show().fadeOut(2e3);
});
 $('#qr-listing-details').on('submit', function(e) {
    if ($('select[name="action"]').val() === 'delete') {
        if (!confirm('Are you sure you want to delete the selected QR codes?')) {
            e.preventDefault();
        }
    }
});
 const requiredColumns = ['id', 'user_id', 'name', 'qr_code', 'url'];
 requiredColumns.forEach(function(column) {
    jQuery('input[name="columns[]"][value="' + column + '"]').prop('disabled', true);
});
 $('#submit_qrcode_setting').on('click', function() {
    var formData = $('#wwt-qrcode-setting-form').serialize();

    $.ajax({
        type: 'POST',
        url: wwtQrCodeGenerator.ajax_url,
        data: formData + '&action=save_qrcode_settings',
        success: function(response) {
            // Clear previous messages
            $('#response-message').empty();

            if (response.success) {
                $('#response-message').html('<div class="notice notice-success is-dismissible"><p>' + response.data + '</p></div>');
            } else {
                $('#response-message').html('<div class="notice notice-error is-dismissible"><p>' + response.data + '</p></div>');
            }

            // Add dismiss functionality to the notification
            $('.is-dismissible').on('click', function() {
                $(this).fadeOut();
            });
        },
        error: function() {
            $('#response-message').html('<div class="notice notice-error is-dismissible"><p>An error occurred. Please try again.</p></div>');

            // Add dismiss functionality to the notification
            $('.is-dismissible').on('click', function() {
                $(this).fadeOut();
            });
        }
    });
});
});