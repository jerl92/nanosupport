/**!
 * NanoSupport Dashboard Scripts
 * Scripts to decorate/manipulate NanoSupport Dashboard widget.
 *
 * @since   1.0.0
 *
 * @author  nanodesigns
 * @package NanoSupport
 */

/**
 * NanoSupport Desk chart
 * ...
 */
var chart = c3.generate({
    bindto: '#ns-chart',
    data: {
        columns: [
            [ns.inspection_label, ns.inspection],
            [ns.open_label, ns.open],
            [ns.solved_label, ns.solved],
            [ns.pending_label, ns.pending],
        ],
        type : 'donut',
    },
    donut: {
        width: 45,
        label: {
            format: function (value, ratio, id) {
              return d3.format('')(value);
            }
        }
    },
    color: {
        pattern: [
            '#3bafda',  //Inspection
            '#f6bb42',  //Open
            '#8cc152',  //Solved
            '#aab2bd'   //Pending
        ]
    },
    size: {
        height: 200
    }
});


/**
 * Agent, personal chart
 * ...
 */
var activity_chart = c3.generate({
    bindto: '#ns-activity-chart',
    data: {
        columns: [
            [ns.inspection_label, ns.my_inspection],
            [ns.open_label, ns.my_open],
            [ns.solved_label, ns.my_solved],
            [ns.pending_label, ns.my_pending],
        ],
        type : 'donut',
    },
    donut: {
        width: 45,
        label: {
            format: function (value, ratio, id) {
              return d3.format('')(value);
            }
        }
    },
    color: {
        pattern: [
            '#3bafda',  //Inspection
            '#f6bb42',  //Open
            '#8cc152',  //Solved
            '#aab2bd'   //Pending
        ]
    },
    size: {
        height: 200
    }
});


jQuery(document).ready(function($) {
    adaptColor('.ns-label-dashboard');
});

function adaptColor(selector) {
    var rgb = jQuery(selector).css("background-color");

    if (rgb.match(/^rgb/)) {
    var a = rgb.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+(?:\.\d+)?))?\)$/),
        r = a[1],
        g = a[2],
        b = a[3];
    }
    var hsp = Math.sqrt(
    0.299 * (r * r) +
    0.587 * (g * g) +
    0.114 * (b * b)
    );
    if (hsp > 127.5) {
        jQuery(selector).css('color', 'black');
    } else {
        jQuery(selector).css('color', 'white');
    }
};