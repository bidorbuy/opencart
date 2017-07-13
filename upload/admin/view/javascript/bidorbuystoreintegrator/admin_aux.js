jQuery(document).ready(function () {
    //var loggingHeader = '<thead> <tr> <td colspan="3" class="left">Logging & Notifications</td> </tr></thead>';
    //Make "Logging & Notifications" table more pretty
    //jQuery(".bobsi-logging-form-table").parent().parent().css("min-height", 0);
    //jQuery(".bobsi-logging-form-table").prepend(loggingHeader);

    jQuery(".submit").parent().attr({"nowrap": "", "id": "cats-middle"});
    jQuery(".submit").parent().prev().attr({"id": "cats-left"});
    jQuery(".submit").parent().next().attr({"id": "cats-right"});

    jQuery("#toolbar-export").attr({"target": "_blank", "href": jQuery("input#tokenExportUrl.bobsi-url").attr("value")});
    jQuery("#toolbar-download").attr({"target": "_blank", "href": jQuery("input#tokenDownloadUrl.bobsi-url").attr("value")});

    jQuery(".copy-button").click(function () {
        jQuery(this).parent().prev().find('.bobsi-url').select();
    });

    //defect #3664
    var max_width = jQuery("#bobsi-inc-categories").width();

    if (jQuery('footer').length) { /*OpenCart 2.x has html5 tags*/
        jQuery("<div id='bobsi-measure_options_width'></div>").insertAfter("footer");
    } else {
        jQuery("<div id='bobsi-measure_options_width'></div>").insertAfter("#footer");
    }
    jQuery(".bobsi-categories-select option").each(function (index) {
        var text = this.innerText ? this.innerText : this.textContent;

        var option_val = jQuery(this).val();
        var span_id = 'option_val_' + option_val;

        jQuery("<span style='float:left;' id='" + span_id + "'>" + text + "</span>").appendTo("#bobsi-measure_options_width");
        var new_option_width = jQuery("#" + span_id).width();

        jQuery(this).css("width", new_option_width + 10 + "px");

    });
    jQuery('.debug').before(jQuery('#bobsi-export-links'));
});
