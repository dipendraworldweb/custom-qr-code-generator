// jQuery(document).ready(function($) {
//     $('.wp-color-picker').wpColorPicker();
//     var imgPreviews = $('#default_logo').val();
//     var eye_frame_name = $('#eye_frame_name').val();
//     var eye_balls_name = $('#eye_balls_name').val();
//     var default_frame = $('#default_frame').val();
//     var logo_preview = $('#logo_preview');
//     var frame_preview = $('#frame_preview');
//     var eye_frame_preview = $('#eye_frame_preview');
//     var eye_balls_preview = $('#eye_balls_preview');
//     if (imgPreviews == 'default') {
//      logo_preview.hide();
//  }
//    // if (default_frame == 'default') {
//    //     frame_preview.hide();
//    // }
//  if (eye_frame_name == 'default') {
//      eye_frame_preview.hide();
//  }   
//  if (eye_balls_name == 'default') {
//      eye_balls_preview.hide();
//  }
// });

// jQuery(document).ready(function($) {
//     jQuery('#template_name').on('change', function() {
//         var value = jQuery(this).val();
//         var settings = {};

//         switch (value) {
//         case 'facebook':
//             settings = {
//                 default_logo: 'facebook',
//                 default_frame: 'default',
//                 eye_frame_name: 'frame14',
//                 eye_balls_name: 'ball16',
//                 qr_code_color: '#2c4270',
//                 qr_eye_color: '#476cb9',
//                 qr_eye_frame_color: '#476cb9',
//                 qrcode_level: 'QR_ECLEVEL_H'
//             };
//             break;
//         case 'youtube-circle':
//             settings = {
//                 default_logo: 'youtube-circle',
//                 default_frame: 'default',
//                 eye_frame_name: 'frame13',
//                 eye_balls_name: 'ball14',
//                 qr_code_color: '#BF2626',
//                 qr_eye_color: '#EE0F0F',
//                 qr_eye_frame_color: '#EE0F0F',
//                 qrcode_level: 'QR_ECLEVEL_H'
//             };
//             break;
//         case 'twitter-circle':
//             settings = {
//                 default_logo: 'twitter-circle',
//                 default_frame: 'default',
//                 eye_frame_name: 'frame5',
//                 eye_balls_name: 'ball11',
//                 qr_code_color: '#55ACEE',
//                 qr_eye_color: '#55ACEE',
//                 qr_eye_frame_color: '#55ACEE',
//                 qrcode_level: 'QR_ECLEVEL_Q'
//             };
//             break;
//         case 'instagram-circle':
//             settings = {
//                 default_logo: 'instagram-circle',
//                 default_frame: 'default',
//                 eye_frame_name: 'frame0',
//                 eye_balls_name: 'ball14',
//                 qr_code_color: '#e1306c',
//                 qr_eye_color: '#e1306c',
//                 qr_eye_frame_color: '#c13584',
//                 qrcode_level: 'QR_ECLEVEL_M'
//             };
//             break;
//         case 'whatsapp-circle':
//             settings = {
//                 default_logo: 'whatsapp-circle',
//                 default_frame: 'default',
//                 eye_frame_name: 'frame2',
//                 eye_balls_name: 'ball2',
//                 qr_code_color: '#1eaa6e',
//                 qr_eye_color: '#25d366',
//                 qr_eye_frame_color: '#25d366',
//                 qrcode_level: 'QR_ECLEVEL_M'
//             };
//             break;
//         case 'gmail':
//             settings = {
//                 default_logo: 'gmail',
//                 default_frame: 'default',
//                 eye_frame_name: 'frame14',
//                 eye_balls_name: 'ball14',
//                 qr_code_color: '#c43e2d',
//                 qr_eye_color: '#ea4335',
//                 qr_eye_frame_color: '#ea4335',
//                 qrcode_level: 'QR_ECLEVEL_Q'
//             };
//             break;
//         case 'linkedin-circle':
//             settings = {
//                 default_logo: 'linkedin-circle',
//                 default_frame: 'default',
//                 eye_frame_name: 'frame0',
//                 eye_balls_name: 'ball0',
//                 qr_code_color: '#0086c9',
//                 qr_eye_color: '#0095cc',
//                 qr_eye_frame_color: '#0095cc',
//                 qrcode_level: 'QR_ECLEVEL_M'
//             };
//             break;
//         case 'default':
//             settings = {
//                 default_logo: 'default',
//                 default_frame: 'default',
//                 eye_frame_name: 'default',
//                 eye_balls_name: 'default',
//                 qr_code_color: '#000000',
//                 qr_eye_color: '#000000',
//                 qr_eye_frame_color: '#000000',
//                 qrcode_level: 'QR_ECLEVEL_M'
//             };
//             break;
//         }

//         // Apply settings
//         $.each(settings, function(key, value) {
//             var element = jQuery(`select[name="${key}"], input[name="${key}"]`);
//             element.val(value).trigger('change');
//         });
//     });
// });

// jQuery(document).ready(function($) {
//   function validateInputsUrl() {
//     var isValid = true;
//             // Validate URL field
//     var url = jQuery('#qrcode_url').val();
//     if (url.length > 75) {
//         jQuery('#url_error').show();
//         isValid = false;
//     } else {
//         jQuery('#url_error').hide();
//     }
// }

//        // Validate Name field
// function validateInputsName() {
//     var isValid = true;
//     var name = jQuery('#qrcode_name').val();
//     var nameRegex = /^[A-Za-z\s]+$/;
//     if (name.length > 30 || !nameRegex.test(name)) {
//         jQuery('#name_error').show();
//         isValid = false;
//     } else {
//         jQuery('#name_error').hide();
//     }

//             // Disable or enable button based on validation
//     jQuery('#wwt-qrcode-generate-form p.submit input#submit').prop('disabled', !isValid);
// }

//         // Bind validation to focusout event on inputs
// jQuery('#qrcode_url').on('focusout', function() {
//     if (jQuery('#qrcode_url').val() != '')  {
//         validateInputsUrl();
//     }
// });
// jQuery('#qrcode_name').on('focusout', function() {
//     if(jQuery('#qrcode_name').val() != '')  {
//         validateInputsName();
//     }
// });
// });

// jQuery(document).ready(function($) {
//     var mediaUploader;

//     $('#upload_logo_button').click(function(e) {
//         e.preventDefault();

//             // If the media uploader already exists, open it.
//         if (mediaUploader) {
//             mediaUploader.open();
//             return;
//         }

//             // Create a new media uploader instance
//         mediaUploader = wp.media({
//             title: 'Select or Upload Logo',
//             button: {
//                 text: 'Use this logo'
//             },
//             multiple: false
//         });

//             // Handle the media selection
//         mediaUploader.on('select', function() {
//             var attachment = mediaUploader.state().get('selection').first().toJSON();
//             var fileSize = attachment.filesizeInBytes;
//             var maxFileSize = 5242880;
//             var fileType = attachment.mime;

//                 // var maxFileSize = 5 * 1024 * 1024; // 5MB in bytes
//             var allowedMimeTypes = ['image/jpeg', 'image/png'];

//                 // Validate file type
//             if (!allowedMimeTypes.includes(fileType)) {
//                 alert('Only JPG and PNG files are allowed.');
//                 return;
//             }

//                 // Validate file size
//             if (fileSize > maxFileSize) {
//                 alert('Maximum file size exceeded (5MB).');
//                 return;
//             }

//                 // If validation passes, update the field and preview image
//             $('#upload_logo_url').val(attachment.url);
//             $('#logo_preview').attr('src', attachment.url).show();
//             $('#logo_previews').hide();
//         });
//         mediaUploader.open();
//     });
// });

// document.addEventListener('DOMContentLoaded', function() {
//     function initializeImagePreview() {
//         updateLogoPreview();
//         updateFramePreview();
//         updateTemplatePreview();
//         updateEyeFramePreview();
//         updateEyeBallsPreview();
//         toggleLogoFields();
//     }

//     function updateLogoPreview() {
//         var selectedLogo = document.getElementById('default_logo').value;
//         var imgPreview = document.getElementById('logo_preview');
//         var pluginLogoImagePath = wwtQrCodeGenerator.pluginLogoImagePath;

//         switch (selectedLogo) {
//         case 'default':
//          imgPreview.style.display = 'none';
//          break; 
//      case 'instagram-circle':
//         imgPreview.style.display = 'inline';
//         imgPreview.src = pluginLogoImagePath + 'instagram-circle.png';
//         break;
//     case 'facebook':
//         imgPreview.style.display = 'inline';
//         imgPreview.src = pluginLogoImagePath + 'facebook.png';
//         break;
//     case 'youtube-circle':
//         imgPreview.style.display = 'inline';
//         imgPreview.src = pluginLogoImagePath + 'youtube-circle.png';
//         break;
//     case 'whatsapp-circle':
//         imgPreview.style.display = 'inline';
//         imgPreview.src = pluginLogoImagePath + 'whatsapp-circle.png';
//         break;
//     case 'linkedin-circle':
//         imgPreview.style.display = 'inline';
//         imgPreview.src = pluginLogoImagePath + 'linkedin-circle.png';
//         break;
//     case 'twitter-circle':
//         imgPreview.style.display = 'inline';
//         imgPreview.src = pluginLogoImagePath + 'twitter-circle.png';
//         break;
//     case 'gmail':
//         imgPreview.style.display = 'inline';
//         imgPreview.src = pluginLogoImagePath + 'gmail.png';
//         break;
//     case 'google-play':
//         imgPreview.style.display = 'inline';
//         imgPreview.src = pluginLogoImagePath + 'google-play.png';
//         break;
//     case 'googleplus-circle':
//         imgPreview.style.display = 'inline';
//         imgPreview.src = pluginLogoImagePath + 'googleplus-circle.png';
//         break;
//     case 'xing-circle':
//         imgPreview.style.display = 'inline';
//         imgPreview.src = pluginLogoImagePath + 'xing-circle.png';
//         break;
//     case 'google-calendar':
//         imgPreview.style.display = 'inline';
//         imgPreview.src = pluginLogoImagePath + 'google-calendar.png';
//         break;
//     case 'google-forms':
//         imgPreview.style.display = 'inline';
//         imgPreview.src = pluginLogoImagePath + 'google-forms.png';
//         break;
//     case 'google-maps':
//         imgPreview.style.display = 'inline';
//         imgPreview.src = pluginLogoImagePath + 'google-maps.png';
//         break;
//     case 'google-meet':
//         imgPreview.style.display = 'inline';
//         imgPreview.src = pluginLogoImagePath + 'google-meet.png';
//         break;
//     case 'google-sheets':
//         imgPreview.style.display = 'inline';
//         imgPreview.src = pluginLogoImagePath + 'google-sheets.png';
//         break;
//     case 'hangouts-meet':
//         imgPreview.style.display = 'inline';
//         imgPreview.src = pluginLogoImagePath + 'hangouts-meet.png';
//         break;
//     case 'spotify':
//         imgPreview.style.display = 'inline';
//         imgPreview.src = pluginLogoImagePath + 'spotify.png';
//         break;
//     case 'telegram':
//         imgPreview.style.display = 'inline';
//         imgPreview.src = pluginLogoImagePath + 'telegram.png';
//         break;
//     default:
//         imgPreview.src = '';
//     }
// }

// function updateTemplatePreview() {
//     var selectedTemplate = document.getElementById('template_name').value;
//     var templatePreview = document.getElementById('template_preview');
//     var pluginTemplateImagePath = wwtQrCodeGenerator.pluginTemplateImagePath;

//     switch (selectedTemplate) {
//     case 'default':
//       templatePreview.style.display = 'inline';
//       templatePreview.src = pluginTemplateImagePath + 'default-1.png';
//       break;   
//   case 'facebook':
//     templatePreview.style.display = 'inline';
//     templatePreview.src = pluginTemplateImagePath + 'facebook.png';
//     break;
// case 'youtube-circle':
//     templatePreview.style.display = 'inline';
//     templatePreview.src = pluginTemplateImagePath + 'youtube.png';
//     break;
// case 'twitter-circle':
//     templatePreview.style.display = 'inline';
//     templatePreview.src = pluginTemplateImagePath + 'twitter.png';
//     break;
// case 'instagram-circle':
//     templatePreview.style.display = 'inline';
//     templatePreview.src = pluginTemplateImagePath + 'instagram.png';
//     break;
// case 'whatsapp-circle':
//     templatePreview.style.display = 'inline';
//     templatePreview.src = pluginTemplateImagePath + 'whatsapp.png';
//     break;
// case 'gmail':
//     templatePreview.style.display = 'inline';
//     templatePreview.src = pluginTemplateImagePath + 'gmail.png';
//     break;
// case 'linkedin-circle':
//     templatePreview.style.display = 'inline';
//     templatePreview.src = pluginTemplateImagePath + 'linkedin.png';
//     break;
// default:
//     templatePreview.src = '';
// }
// }

// function updateFramePreview() {
//     var selectedFrame = document.getElementById('default_frame').value;
//     var framePreview = document.getElementById('frame_preview');
//     var pluginFrameImagePath = wwtQrCodeGenerator.pluginFrameImagePath;

//     switch (selectedFrame) {
//     case 'default':
//       framePreview.style.display = 'inline';
//       framePreview.src = pluginFrameImagePath + 'default-1.png';
//       break;   
//   case 'balloon-bottom':
//     framePreview.style.display = 'inline';
//     framePreview.src = pluginFrameImagePath + 'balloon-bottom.png';
//     break;
// case 'balloon-bottom-1':
//     framePreview.style.display = 'inline';
//     framePreview.src = pluginFrameImagePath + 'balloon-bottom-1.png';
//     break;
// case 'balloon-top':
//     framePreview.style.display = 'inline';
//     framePreview.src = pluginFrameImagePath + 'balloon-top.png';
//     break;
// case 'balloon-top-2':
//     framePreview.style.display = 'inline';
//     framePreview.src = pluginFrameImagePath + 'balloon-top-2.png';
//     break;
// case 'banner-bottom':
//     framePreview.style.display = 'inline';
//     framePreview.src = pluginFrameImagePath + 'banner-bottom.png';
//     break;
// case 'banner-bottom-3':
//     framePreview.style.display = 'inline';
//     framePreview.src = pluginFrameImagePath + 'banner-bottom-3.png';
//     break;
// case 'banner-top':
//     framePreview.style.display = 'inline';
//     framePreview.src = pluginFrameImagePath + 'banner-top.png';
//     break;
// case 'banner-top-4':
//     framePreview.style.display = 'inline';
//     framePreview.src = pluginFrameImagePath + 'banner-top-4.png';
//     break;
// case 'box-bottom':
//     framePreview.style.display = 'inline';
//     framePreview.src = pluginFrameImagePath + 'box-bottom.png';
//     break;
// case 'box-bottom-5':
//     framePreview.style.display = 'inline';
//     framePreview.src = pluginFrameImagePath + 'box-bottom-5.png';
//     break;
// case 'box-Top':
//     framePreview.style.display = 'inline';
//     framePreview.src = pluginFrameImagePath + 'box-Top.png';
//     break;
// case 'box-Top-6':
//     framePreview.style.display = 'inline';
//     framePreview.src = pluginFrameImagePath + 'box-Top-6.png';
//     break;
// case 'focus-8-lite':
//     framePreview.style.display = 'inline';
//     framePreview.src = pluginFrameImagePath + 'focus-8-lite.png';
//     break;
// case 'focus-lite':
//     framePreview.style.display = 'inline';
//     framePreview.src = pluginFrameImagePath + 'focus-lite.png';
//     break;
// default:
//     framePreview.src = '';
// }
// }

// function updateEyeFramePreview() {
//     var selectedEyeFrame = document.getElementById('eye_frame_name').value;
//     var eyeFramePreview = document.getElementById('eye_frame_preview');
//     var pluginEyeFrameImagePath = wwtQrCodeGenerator.pluginEyeFrameImagePath;

//     switch (selectedEyeFrame) {
//     case 'default':
//        eyeFramePreview.style.display = 'none';
//        break;   
//    case 'frame0':
//     eyeFramePreview.style.display = 'inline';
//     eyeFramePreview.src = pluginEyeFrameImagePath + 'frame0.png';
//     break;
// case 'frame1':
//     eyeFramePreview.style.display = 'inline';
//     eyeFramePreview.src = pluginEyeFrameImagePath + 'frame1.png';
//     break;
// case 'frame2':
//     eyeFramePreview.style.display = 'inline';
//     eyeFramePreview.src = pluginEyeFrameImagePath + 'frame2.png';
//     break;
// case 'frame3':
//     eyeFramePreview.style.display = 'inline';
//     eyeFramePreview.src = pluginEyeFrameImagePath + 'frame3.png';
//     break;
// case 'frame4':
//     eyeFramePreview.style.display = 'inline';
//     eyeFramePreview.src = pluginEyeFrameImagePath + 'frame4.png';
//     break;
// case 'frame5':
//     eyeFramePreview.style.display = 'inline';
//     eyeFramePreview.src = pluginEyeFrameImagePath + 'frame5.png';
//     break;
// case 'frame6':
//     eyeFramePreview.style.display = 'inline';
//     eyeFramePreview.src = pluginEyeFrameImagePath + 'frame6.png';
//     break;
// case 'frame7':
//     eyeFramePreview.style.display = 'inline';
//     eyeFramePreview.src = pluginEyeFrameImagePath + 'frame7.png';
//     break;
// case 'frame8':
//     eyeFramePreview.style.display = 'inline';
//     eyeFramePreview.src = pluginEyeFrameImagePath + 'frame8.png';
//     break;
// case 'frame9':
//     eyeFramePreview.style.display = 'inline';
//     eyeFramePreview.src = pluginEyeFrameImagePath + 'frame9.png';
//     break;
// case 'frame10':
//     eyeFramePreview.style.display = 'inline';
//     eyeFramePreview.src = pluginEyeFrameImagePath + 'frame10.png';
//     break;
// case 'frame11':
//     eyeFramePreview.style.display = 'inline';
//     eyeFramePreview.src = pluginEyeFrameImagePath + 'frame11.png';
//     break;
// case 'frame12':
//     eyeFramePreview.style.display = 'inline';
//     eyeFramePreview.src = pluginEyeFrameImagePath + 'frame12.png';
//     break;
// case 'frame13':
//     eyeFramePreview.style.display = 'inline';
//     eyeFramePreview.src = pluginEyeFrameImagePath + 'frame13.png';
//     break;
// case 'frame14':
//     eyeFramePreview.style.display = 'inline';
//     eyeFramePreview.src = pluginEyeFrameImagePath + 'frame14.png';
//     break;
// default:
//     eyeFramePreview.style.display = 'inline';
//     eyeFramePreview.src = 'frame0.png';
// }
// }

// function updateEyeBallsPreview() {
//     var selectedEyeBall = document.getElementById('eye_balls_name').value;
//     var eye_balls_preview = document.getElementById('eye_balls_preview');
//     var pluginEyeBallsImagePath = wwtQrCodeGenerator.pluginEyeBallsImagePath;

//     switch (selectedEyeBall) {
//     case 'default':
//        eye_balls_preview.style.display = 'none';
//        break;
//    case 'ball0':
//     eye_balls_preview.style.display = 'inline';
//     eye_balls_preview.src = pluginEyeBallsImagePath + 'ball0.png';
//     break;
// case 'ball1':
//     eye_balls_preview.style.display = 'inline';
//     eye_balls_preview.src = pluginEyeBallsImagePath + 'ball1.png';
//     break;
// case 'ball2':
//     eye_balls_preview.style.display = 'inline';
//     eye_balls_preview.src = pluginEyeBallsImagePath + 'ball2.png';
//     break;
// case 'ball3':
//     eye_balls_preview.style.display = 'inline';
//     eye_balls_preview.src = pluginEyeBallsImagePath + 'ball3.png';
//     break;
// case 'ball4':
//     eye_balls_preview.style.display = 'inline';
//     eye_balls_preview.src = pluginEyeBallsImagePath + 'ball4.png';
//     break;
// case 'ball5':
//     eye_balls_preview.style.display = 'inline';
//     eye_balls_preview.src = pluginEyeBallsImagePath + 'ball5.png';
//     break;
// case 'ball6':
//     eye_balls_preview.style.display = 'inline';
//     eye_balls_preview.src = pluginEyeBallsImagePath + 'ball6.png';
//     break;
// case 'ball7':
//     eye_balls_preview.style.display = 'inline';
//     eye_balls_preview.src = pluginEyeBallsImagePath + 'ball7.png';
//     break;
// case 'ball8':
//     eye_balls_preview.style.display = 'inline';
//     eye_balls_preview.src = pluginEyeBallsImagePath + 'ball8.png';
//     break;
// case 'ball9':
//     eye_balls_preview.style.display = 'inline';
//     eye_balls_preview.src = pluginEyeBallsImagePath + 'ball9.png';
//     break;
// case 'ball10':
//     eye_balls_preview.style.display = 'inline';
//     eye_balls_preview.src = pluginEyeBallsImagePath + 'ball10.png';
//     break;
// case 'ball11':
//     eye_balls_preview.style.display = 'inline';
//     eye_balls_preview.src = pluginEyeBallsImagePath + 'ball11.png';
//     break;
// case 'ball12':
//     eye_balls_preview.style.display = 'inline';
//     eye_balls_preview.src = pluginEyeBallsImagePath + 'ball12.png';
//     break;
// case 'ball13':
//     eye_balls_preview.style.display = 'inline';
//     eye_balls_preview.src = pluginEyeBallsImagePath + 'ball13.png';
//     break;
// case 'ball14':
//     eye_balls_preview.style.display = 'inline';
//     eye_balls_preview.src = pluginEyeBallsImagePath + 'ball14.png';
//     break;
// case 'ball15':
//     eye_balls_preview.style.display = 'inline';
//     eye_balls_preview.src = pluginEyeBallsImagePath + 'ball15.png';
//     break;
// case 'ball16':
//     eye_balls_preview.style.display = 'inline';
//     eye_balls_preview.src = pluginEyeBallsImagePath + 'ball16.png';
//     break;
// case 'ball17':
//     eye_balls_preview.style.display = 'inline';
//     eye_balls_preview.src = pluginEyeBallsImagePath + 'ball17.png';
//     break;
// case 'ball18':
//     eye_balls_preview.style.display = 'inline';
//     eye_balls_preview.src = pluginEyeBallsImagePath + 'ball18.png';
//     break;
// case 'ball19':
//     eye_balls_preview.style.display = 'inline';
//     eye_balls_preview.src = pluginEyeBallsImagePath + 'ball19.png';
//     break;
// case 'ball20':
//     eye_balls_preview.style.display = 'inline';
//     eye_balls_preview.src = pluginEyeBallsImagePath + 'ball20.png';
//     break;
// case 'ball21':
//     eye_balls_preview.style.display = 'inline';
//     eye_balls_preview.src = pluginEyeBallsImagePath + 'ball21.png';
//     break;
// default:
//     eye_balls_preview.style.display = 'inline';
//     eye_balls_preview.src = 'ball0.png';
// }
// }

// function toggleLogoFields() {
//     var customLogoOption = document.getElementById('custom_logo_option');
//     var uploadLogoOption = document.getElementById('upload_logo_option');
//     var defaultLogoSelect = document.getElementById('default_logo');
//     var uploadLogoInput = document.getElementById('upload_logo_button');
//     var logoPreview = document.getElementById('logo_preview');
//     var logoPreviews = document.getElementById('logo_previews');

//     if (customLogoOption.checked) {
//         defaultLogoSelect.style.display = 'inline';
//         uploadLogoInput.style.display = 'none';
//         logoPreview.style.display = 'inline';
//         if (logoPreviews) {
//             logoPreviews.style.display = 'none';
//         }
//     } else if (uploadLogoOption.checked) {
//         defaultLogoSelect.style.display = 'none';
//         uploadLogoInput.style.display = 'inline';
//         logoPreview.style.display = 'none';
//         if (logoPreviews) {
//             logoPreviews.style.display = 'inline';
//         }
//     }

//     if (defaultLogoSelect.value == 'default') {
//      logoPreview.style.display = 'none';
//  }
// }

// document.getElementById('custom_logo_option').addEventListener('change', toggleLogoFields);
// document.getElementById('upload_logo_option').addEventListener('change', toggleLogoFields);
// initializeImagePreview();

// jQuery(document).on('change', 'select[name="default_logo"]', updateLogoPreview);
// jQuery(document).on('change', 'select[name="default_frame"]', updateFramePreview);
// jQuery(document).on('change', 'select[name="template_name"]', updateTemplatePreview);
// jQuery(document).on('change', 'select[name="eye_frame_name"]', updateEyeFramePreview);
// jQuery(document).on('change', 'select[name="eye_balls_name"]', updateEyeBallsPreview);
// });