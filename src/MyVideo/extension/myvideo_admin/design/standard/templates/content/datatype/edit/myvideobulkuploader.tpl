<div id="dropbox">
    <span class="message">Drop images here to upload. <br /><i>(they will only be visible to you)</i></span>
</div>

{literal}
    <style type="text/css">
        #dropbox{
            background:url('/extension/myvideo_admin/design/admin/images/uploader/background_tile_3.jpg');

            border-radius:3px;
            position: relative;
            margin:80px auto 90px;
            min-height: 290px;
            overflow: hidden;
            padding-bottom: 40px;
            width: 990px;

            box-shadow:0 0 4px rgba(0,0,0,0.3) inset,0 -3px 2px rgba(0,0,0,0.1);
        }


        #dropbox .message{
            font-size: 11px;
            text-align: center;
            padding-top:160px;
            display: block;
        }

        #dropbox .message i{
            color:#ccc;
            font-size:10px;
        }

        #dropbox:before{
            border-radius:3px 3px 0 0;
        }
        #dropbox .preview{
            width:245px;
            height: 215px;
            float:left;
            margin: 55px 0 0 60px;
            position: relative;
            text-align: center;
        }

        #dropbox .preview img{
            max-width: 240px;
            max-height:180px;
            border:3px solid #fff;
            display: block;

            box-shadow:0 0 2px #000;
        }

        #dropbox .imageHolder{
            display: inline-block;
            position:relative;
        }

        #dropbox .uploaded{
            position: absolute;
            top:0;
            left:0;
            height:100%;
            width:100%;
            background: url('/extension/myvideo_admin/design/admin/images/uploader/done.png') no-repeat center center rgba(255,255,255,0.5);
            display: none;
        }

        #dropbox .preview.done .uploaded{
            display: block;
        }
        #dropbox .progressHolder{
            position: absolute;
            background-color:#252f38;
            height:12px;
            width:100%;
            left:0;
            bottom: 0;

            box-shadow:0 0 2px #000;
        }

        #dropbox .progress{
            background-color:#2586d0;
            position: absolute;
            height:100%;
            left:0;
            width:0;

            box-shadow: 0 0 1px rgba(255, 255, 255, 0.4) inset;

            -moz-transition:0.25s;
            -webkit-transition:0.25s;
            -o-transition:0.25s;
            transition:0.25s;
        }

        #dropbox .preview.done .progress{
            width:100% !important;
        }
    </style>
    <script type="text/javascript">
        $(function(){

            var url = document.URL.split('/').reverse();
            var galleryId = window.ez.currentContentObjectId;

            var dropbox = $('#dropbox'),
                    message = $('.message', dropbox);

            dropbox.filedrop({
                // The name of the $_FILES entry:
                paramname:'file',

                maxfiles: 5,
                maxfilesize: 2,
                url: '/massupload?id='+galleryId,

                uploadFinished:function(i,file,response){
                    $.data(file).addClass('done');
                    $.ajax({
                        type: "GET",
                        url: '/massupload/store?id='+galleryId,
                        data: {},
                        success: function(data){
                            $('#ajaxTarget').empty();
                            $('#ajaxTarget').append(data);
                        }
                        ,error: function() {
                        }
                    });
                },

                error: function(err, file) {
                    switch(err) {
                        case 'BrowserNotSupported':
                            showMessage('Your browser does not support HTML5 file uploads!');
                            break;
                        case 'TooManyFiles':
                            alert('Too many files! Please select 5 at most! (configurable)');
                            break;
                        case 'FileTooLarge':
                            alert(file.name+' is too large! Please upload files up to 2mb (configurable).');
                            break;
                        default:
                            break;
                    }
                },

                // Called before each upload is started
                beforeEach: function(file){
                    if(!file.type.match(/^image\//)){
                        alert('Only images are allowed!');

                        // Returning false will cause the
                        // file to be rejected
                        return false;
                    }
                },

                uploadStarted:function(i, file, len){
                    createImage(file);
                },

                progressUpdated: function(i, file, progress) {
                    $.data(file).find('.progress').width(progress);
                }

            });

            var template = '<div class="preview">'+
                    '<span class="imageHolder">'+
                    '<img />'+
                    '<span class="uploaded"></span>'+
                    '</span>'+
                    '<div class="progressHolder">'+
                    '<div class="progress"></div>'+
                    '</div>'+
                    '</div>';


            function createImage(file){

                var preview = $(template),
                        image = $('img', preview);

                var reader = new FileReader();

                image.width = 100;
                image.height = 100;

                reader.onload = function(e){

                    // e.target.result holds the DataURL which
                    // can be used as a source of the image:

                    image.attr('src',e.target.result);
                };

                // Reading the file as a DataURL. When finished,
                // this will trigger the onload function above:
                reader.readAsDataURL(file);

                message.hide();
                preview.appendTo(dropbox);

                // Associating a preview container
                // with the file, using jQuery's $.data():

                $.data(file,preview);
            }

            function showMessage(msg){
                message.html(msg);
            }

        });
    </script>
{/literal}
