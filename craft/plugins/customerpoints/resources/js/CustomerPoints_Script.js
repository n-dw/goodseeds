/**
 * Customer Points plugin for Craft CMS
 *
 * Customer Points JS
 *
 * @author    Nathan de Waard
 * @copyright Copyright (c) 2017 Nathan de Waard
 * @link      https://github.com/n-dw
 * @package   CustomerPoints
 * @since     1.0.0
 */
// For Permissions panel, handle checkboxes
$(function() {

    $(document).on('click', '.customer-points-tabs a.tab', function(e) {
        e.preventDefault();

        $('.customer-points-tabs a.tab').removeClass('sel');
        $(this).addClass('sel');

        $('.settings-pane').addClass('hidden');
        
        var paneToShow = $(this).attr('href');
        $(paneToShow).removeClass('hidden');
       
    });
});