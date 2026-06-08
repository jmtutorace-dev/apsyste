<script src="../bower_components/jquery/dist/jquery.min.js"></script>
<script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="../bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="../bower_components/moment/min/moment.min.js"></script>
<script src="../bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="../bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<script src="../bower_components/fastclick/lib/fastclick.js"></script>
<script src="../dist/js/adminlte.min.js"></script>

<script>
$(function () {

    /* highlight active sidebar link */
    var url = window.location.href.split('?')[0];
    $('ul.sidebar-menu a').filter(function () {
        return this.href.split('?')[0] == url;
    }).parent().addClass('active');

    /* DataTables (any table with id="example1") */
    if ($('#example1').length) {
        $('#example1').DataTable({
            'paging': true,
            'lengthChange': true,
            'searching': true,
            'ordering': true,
            'info': true,
            'autoWidth': false
        });
    }

    /* Date range picker for the payslip period */
    if ($('#date_range').length) {
        $('#date_range').daterangepicker({
            locale: { format: 'MM/DD/YYYY' }
        });
    }

    /* Flash messages: ensure a close button + auto-dismiss success/info */
    $('.content .alert').each(function () {
        var $a = $(this);
        if (!$a.find('.close').length) {
            $a.addClass('alert-dismissible')
              .prepend('<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>');
        }
        if ($a.hasClass('alert-success') || $a.hasClass('alert-info')) {
            setTimeout(function () { $a.fadeOut(400, function () { $(this).remove(); }); }, 4500);
        }
    });

    /* Loading state on form submit. IMPORTANT: defer disabling the button to
       the next tick — disabling it during the submit event drops its
       name/value from the POST (which broke login). */
    $('form').on('submit', function () {
        var $btn = $(this).find('button[type=submit]').first();
        if ($btn.length && !$btn.data('busy')) {
            $btn.data('busy', true).data('label', $btn.html());
            setTimeout(function () {
                $btn.prop('disabled', true)
                    .html('<i class="fa fa-spinner fa-spin"></i> Please wait...');
            }, 0);
            setTimeout(function () {
                $btn.prop('disabled', false).html($btn.data('label')).removeData('busy');
            }, 6000);
        }
    });

});
</script>
