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