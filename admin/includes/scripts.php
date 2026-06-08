<!-- jQuery 3 -->
<script src="../bower_components/jquery/dist/jquery.min.js"></script>

<!-- jQuery UI 1.11.4 -->
<script src="../bower_components/jquery-ui/jquery-ui.min.js"></script>

<!-- DataTables -->
<script src="../bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
$.widget.bridge('uibutton', $.ui.button);
</script>

<!-- Bootstrap 3.3.7 -->
<script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

<!-- Morris.js charts -->
<script src="../bower_components/raphael/raphael.min.js"></script>
<script src="../bower_components/morris.js/morris.min.js"></script>

<!-- ChartJS -->
<script src="../bower_components/chart.js/Chart.js"></script>

<!-- Sparkline -->
<script src="../bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>

<!-- jvectormap -->
<script src="../plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="../plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>

<!-- jQuery Knob Chart -->
<script src="../bower_components/jquery-knob/dist/jquery.knob.min.js"></script>

<!-- Moment -->
<script src="../bower_components/moment/min/moment.min.js"></script>

<!-- Date Range Picker -->
<script src="../bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>

<!-- Bootstrap Time Picker -->
<script src="../plugins/timepicker/bootstrap-timepicker.min.js"></script>

<!-- Bootstrap WYSIHTML5 -->
<script src="../plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>

<!-- Slimscroll -->
<script src="../bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>

<!-- FastClick -->
<script src="../bower_components/fastclick/lib/fastclick.js"></script>

<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>

<!-- AdminLTE Dashboard -->
<!--
<script src="../dist/js/pages/dashboard.js"></script>
-->

<!-- AdminLTE Demo -->
<script src="../dist/js/demo.js"></script>

<script>
$(function () {

    var url = window.location;

    $('ul.sidebar-menu a').filter(function () {
        return this.href == url;
    }).parent().addClass('active');

    $('ul.treeview-menu a').filter(function () {
        return this.href == url;
    }).parentsUntil(".sidebar-menu > .treeview-menu").addClass('active');

});
</script>

<script>
$(function () {

    /* Timepicker */
    $('.timepicker').timepicker({
        showInputs: false
    });

    /* Date Range Picker */
    if ($('#reservation').length) {
        $('#reservation').daterangepicker();
    }

    /* Date Range Picker with Time */
    if ($('#reservationtime').length) {
        $('#reservationtime').daterangepicker({
            timePicker: true,
            timePickerIncrement: 30,
            locale: {
                format: 'MM/DD/YYYY h:mm A'
            }
        });
    }

    /* Date Range Button */
    if ($('#daterange-btn').length) {

        $('#daterange-btn').daterangepicker(
            {
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [
                        moment().subtract(1, 'days'),
                        moment().subtract(1, 'days')
                    ],
                    'Last 7 Days': [
                        moment().subtract(6, 'days'),
                        moment()
                    ],
                    'Last 30 Days': [
                        moment().subtract(29, 'days'),
                        moment()
                    ],
                    'This Month': [
                        moment().startOf('month'),
                        moment().endOf('month')
                    ],
                    'Last Month': [
                        moment().subtract(1, 'month').startOf('month'),
                        moment().subtract(1, 'month').endOf('month')
                    ]
                },
                startDate: moment().subtract(29, 'days'),
                endDate: moment()
            },
            function (start, end) {

                $('#daterange-btn span').html(
                    start.format('MMMM D, YYYY') +
                    ' - ' +
                    end.format('MMMM D, YYYY')
                );

            }
        );

    }

});
</script>

<!-- ============================================================
     SITE-WIDE UX ENHANCEMENTS
     ============================================================ -->
<script>
$(function () {

    /* 1) Flash messages: guarantee a close button + auto-dismiss success/info */
    $('.content .alert, .content-header .alert').each(function () {
        var $a = $(this);
        if (!$a.find('.close').length) {
            $a.addClass('alert-dismissible')
              .prepend('<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>');
        }
        if ($a.hasClass('alert-success') || $a.hasClass('alert-info')) {
            setTimeout(function () { $a.fadeOut(400, function () { $(this).remove(); }); }, 4500);
        }
    });

    /* 2) Robust active sidebar link (matches by file name, ignores ?query) */
    var path = (window.location.pathname.split('/').pop() || 'home.php');
    $('ul.sidebar-menu li > a').each(function () {
        var href = ($(this).attr('href') || '').split('/').pop().split('?')[0];
        if (href && href === path) {
            $(this).parent().addClass('active');
            $(this).parents('li.treeview').addClass('active menu-open');
        }
    });

    /* 3) Enable Bootstrap tooltips wherever used */
    $('[data-toggle="tooltip"]').tooltip();

    /* 4) Loading state on CRUD modal submit buttons (real page-reload POSTs).
          IMPORTANT: defer disabling to the next tick — a button disabled during
          the submit event is excluded from the POST, which would drop the
          add/edit/delete name the handlers check for. */
    $('.modal form').on('submit', function () {
        var $btn = $(this).find('button[type=submit]').first();
        if ($btn.length && !$btn.data('busy')) {
            $btn.data('busy', true).data('label', $btn.html());
            setTimeout(function () {
                $btn.prop('disabled', true)
                    .html('<i class="fa fa-spinner fa-spin"></i> Please wait...');
            }, 0);
            setTimeout(function () {            // safety re-enable if the submit is cancelled
                $btn.prop('disabled', false).html($btn.data('label')).removeData('busy');
            }, 8000);
        }
    });

});
</script>