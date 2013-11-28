$(document).ready(function () {
    $('input[value="Bearbeiten"]').addClass('defaultbutton');

    // need this because some legacy ez extension forces our button to display none.
    $('input.trigger-preview-input').click(function (e) {
        e.preventDefault();
        var previewUrl = ez.previewUrl;
        if (previewUrl.indexOf('http://') != 0) {
            previewUrl = 'http://' + previewUrl;
        }

        var previewWindow = window.open('', 'PreviewWindow' + new Date().getUTCMilliseconds());
        var $previewHtmlDocument = '<html><head><title>Preview</title><meta http-equiv="refresh" content="5; url=' + previewUrl + '"></head><body>Please wait ...</body></html>';

        previewWindow.document.open();
        previewWindow.document.write($previewHtmlDocument);
        previewWindow.document.close();

        $(this).next().click();
    })

    // Delete redis preview key when discard the draft
    var redisKeysAreDeleted = false;
    var $discardButton = $('input[name="DiscardButton"]');
    $discardButton.click(function (e) {

        if (redisKeysAreDeleted === true) {
            return true;
        }

        if (!confirm(ez.translations.confirmDiscard)) {
            return false;
        }

        document.editform.DiscardConfirm.value = "0";
        e.preventDefault();

        var request = $.ajax({
            url: '/utilities/discard_redis_preview',
            type: "GET",
            data: { objectId: ez.currentContentObjectId},
            dataType: "json"
        });

        request.success(function (json) {
            if (typeof json !== "undefined") {
                if (typeof json.error !== "undefined") {
                    alert(json.error);
                } else {
                    redisKeysAreDeleted = true;
                    $discardButton.click();
                }
            }
        })

    })

    $('.show-hide-tabs').click(function () {
        $(this).parent().parent().children('.ezcca-edit-datatype-ezobjectrelation').toggle();
        $(this).toggleClass('active');

        return false;
    })

    if ($('.ezcca-edit-datatype-ezmatrix').length > 0) {
        var rowCount = $('.ezcca-edit-datatype-ezmatrix table tr').length;
        var contentObjectAttributeId = $('.ezcca-edit-datatype-ezmatrix input[name="ContentObjectAttribute_id[]"]').val();
        if (rowCount > 49) {
            $('.ezcca-edit-datatype-ezmatrix input[name="CustomActionButton[' + contentObjectAttributeId + '_new_row]"]').attr('disabled', 'disabled');
        }
    }

    if ($('#metadata_robots_select').length > 0) {
        $(this).change(function () {
            var selected = $('#metadata_robots_select option:selected').map(function () {
                return this.value
            }).get().join(', ');
            $('#metadata_robots').val(selected);
        })

        $.each($('#metadata_robots').val().split(','), function (index, value) {
            $('#metadata_robots_select option[value=' + value + ']').attr('selected', 'selected');
        });
    }
});

