</div> 
</div>
<script src="<?= base_url('assets/js/vendor-all.min.js') ?>"></script>
<script src="<?= base_url('assets/js/plugins/bootstrap.min.js') ?>"></script>
<script src="<?= base_url('assets/js/ripple.js') ?>"></script>
<script src="<?= base_url('assets/js/pcoded.min.js') ?>"></script>
<script src="<?= base_url('assets/plugins/select2/js/select2.full.min.js') ?>"></script>
<script src="<?= base_url('assets/plugins/moment/moment.min.js') ?>"></script>
<script src="<?= base_url('assets/plugins/daterangepicker/daterangepicker.js') ?>"></script>
<script src="<?= base_url('assets/plugins/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') ?>"></script>
<script src="<?= base_url('assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('assets/plugins/jszip/jszip.min.js') ?>"></script>
<script src="<?= base_url('assets/plugins/pdfmake/pdfmake.min.js') ?>"></script>
<script src="<?= base_url('assets/plugins/pdfmake/vfs_fonts.js') ?>"></script>
<script src="<?= base_url('assets/plugins/datatables-buttons/js/buttons.html5.min.js') ?>"></script>
<script src="<?= base_url('assets/plugins/datatables-buttons/js/buttons.print.min.js') ?>"></script>
<script>
    <?php
    $path_logo = FCPATH . 'assets/dist/img/logoUT.png'; 
    $base64_logo = '';
    if (file_exists($path_logo)) {
        $type = pathinfo($path_logo, PATHINFO_EXTENSION);
        $data = file_get_contents($path_logo);
        $base64_logo = 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    $path_logo_kanan = FCPATH . 'assets/dist/img/logo-ewako1.png'; 
    $base64_logo_kanan = '';
    if (file_exists($path_logo_kanan)) {
        $type2 = pathinfo($path_logo_kanan, PATHINFO_EXTENSION);
        $data2 = file_get_contents($path_logo_kanan);
        $base64_logo_kanan = 'data:image/' . $type2 . ';base64,' . base64_encode($data2);
    }
    ?>
  
$(function () {

    if ($('.select2').length) {
        $('.select2').select2({ theme: 'bootstrap4' });
    }

    if ($("#example1").length) {
        $("#example1").DataTable({
            "responsive": true, 
            "paging": true,
            "lengthChange": true,
            "pageLength": 10,
            "autoWidth": false,
            "dom": "<'row mb-3'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4 text-center'B><'col-sm-12 col-md-4'f>>" +
                   "<'row'<'col-sm-12'tr>>" +
                   "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            "buttons": [
                {
                    extend: 'pdfHtml5', 
                    text: '<i class="fas fa-file-pdf mr-1"></i> PDF',
                    className: 'btn btn-danger btn-sm text-white shadow-sm mr-1',
                    init: function(api, node, config) {
                       $(node).removeClass('btn-secondary'); 
                    },
                    orientation: 'landscape', 
                    pageSize: 'A4',
                    title: '', 
                    exportOptions: {
                        columns: ':visible:not(.no-export)',
                        format: {
                            body: function (data, row, column, node) {
                                if (data === null || data === undefined) return '';
                                return data.toString().replace(/<br\s*\/?>/ig, "\n").replace(/<[^>]*>?/gm, '');
                            }
                        }
                    },
                    customize: function (doc) {
                        doc.defaultStyle.fontSize = 9;
                        doc.styles.tableHeader.fontSize = 10;
                        doc.styles.tableHeader.alignment = 'center';
                        doc.styles.tableHeader.fillColor = '#e9ecef';
                        doc.styles.tableHeader.color = 'black';
                        
                        var objTable = doc.content.find(function(c) { return c.table !== undefined; });
                        
                        if (objTable) { 
                            var colCount = objTable.table.body[0].length;
                            var widths = [];
                            for (var i = 0; i < colCount; i++) {
                                widths.push('*');
                            }
                            objTable.table.widths = widths; 
                            
                            objTable.layout = {
                                hLineWidth: function(i) { return 0.5; },
                                vLineWidth: function(i) { return 0.5; },
                                hLineColor: function(i) { return '#aaa'; },
                                vLineColor: function(i) { return '#aaa'; },
                                paddingLeft: function(i) { return 4; },
                                paddingRight: function(i) { return 4; }
                            };
                        }

                        const urlParams = new URLSearchParams(window.location.search);
                        const dateRange = urlParams.get('date_range');
                        const teksPeriode = dateRange ? dateRange : 'Semua Periode';

                        let logoBase64 = '<?= isset($base64_logo) ? $base64_logo : "" ?>';
                        let logoKananBase64 = '<?= isset($base64_logo_kanan) ? $base64_logo_kanan : "" ?>';

                        let kolomKiri = (logoBase64 !== '' && logoBase64.indexOf('data:image') !== -1) ? { image: logoBase64, width: 50 } : { text: '', width: 50 };
                        let kolomKanan = (logoKananBase64 !== '' && logoKananBase64.indexOf('data:image') !== -1) ? { image: logoKananBase64, width: 50 } : { text: '', width: 50 };

                        let kopSurat = {
                            columns: [
                                kolomKiri, 
                                {
                                    text: [
                                        { text: 'MONITORING TOOLS\n', fontSize: 16, bold: true },
                                        { text: 'PT. United Tractors Tbk. Cabang Ujung Pandang\n', fontSize: 12 },
                                    ],
                                    alignment: 'center', margin: [0, 5, 0, 0] 
                                },
                                kolomKanan
                            ],
                            margin: [0, 0, 0, 5]
                        };
                        
                        let headerTambahan = {
                            stack: [
                                { canvas: [{ type: 'line', x1: 0, y1: 0, x2: 760, y2: 0, lineWidth: 1.5 }] }, 
                                { text: 'Periode Tanggal: ' + teksPeriode, alignment: 'center', fontSize: 11, bold: true, margin: [0, 10, 0, 15] }
                            ]
                        };

                        doc.content.unshift(headerTambahan);
                        doc.content.unshift(kopSurat);
                        
                        doc.content.push({
                            margin: [0, 40, 0, 0],
                            table: {
                                widths: ['33%', '34%', '33%'],
                                body: [
                                    [
                                        { text: 'Mengetahui,\nService Dept. Head', alignment: 'center', border: [false, false, false, false], margin: [0, 0, 0, 60] },
                                        { text: 'Disetujui Oleh,\nSupervisor', alignment: 'center', border: [false, false, false, false], margin: [0, 0, 0, 60] },
                                        { text: 'Dibuat Oleh,\nToolkeeper', alignment: 'center', border: [false, false, false, false], margin: [0, 0, 0, 60] }
                                    ],
                                    [
                                        { text: '( ____________________________ )', alignment: 'center', border: [false, false, false, false] },
                                        { text: '( ____________________________ )', alignment: 'center', border: [false, false, false, false] },
                                        { text: '( ____________________________ )', alignment: 'center', border: [false, false, false, false] }
                                    ]
                                ]
                            }
                        });
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print mr-1"></i> Print', 
                    className: 'btn btn-info btn-sm text-white shadow-sm', 
                    init: function(api, node, config) {
                       $(node).removeClass('btn-secondary'); 
                    },
                    title: '', 
                    exportOptions: { columns: ':visible:not(.no-export)' }, 
                    messageTop: function () {
                        return ''; 
                    },
                    customize: function (win) {
                        win.document.title = "Print - Monitoring Tools PT. UT";
                        
                        const urlParams = new URLSearchParams(window.location.search);
                        const dateRange = urlParams.get('date_range');
                        const teksPeriode = dateRange ? dateRange : 'Semua Periode';
                        
                        $(win.document.head).find('link').remove(); 
                        $(win.document.head).find('style').remove();
                        $(win.document.head).append(
                            '<style>body { font-family: Arial, sans-serif; background-color: #fff; color: #000; margin: 20px; } table { border-collapse: collapse; width: 100%; margin-top: 15px; font-size: 12px; } table th, table td { border: 1px solid #000; padding: 8px; text-align: left; } table th { background-color: #e9ecef !important; text-align: center; -webkit-print-color-adjust: exact; } h3, p { text-align: center; margin: 0; } .ttd-box { width: 100%; margin-top: 50px; table-layout: fixed; border-collapse: collapse; border: none; } .ttd-box td { text-align: center; border: none !important; padding: 0; } .ttd-space { height: 80px; }</style>'
                        );

                        $(win.document.body).prepend(
                            '<div style="margin-bottom: 20px; position: relative; min-height: 80px;">' +
                                '<img src="<?= base_url("assets/dist/img/logout.png") ?>" style="position: absolute; left: 0; top: 0; width: 70px; height: auto;" />' +
                                '<img src="<?= base_url("assets/dist/img/logo-ewako1.png") ?>" style="position: absolute; right: 0; top: 0; width: 70px; height: auto;" />' +
                                '<div style="text-align: center; padding-top: 10px;">' +
                                    '<h3 style="font-weight: bold; font-size: 22px;">MONITORING TOOLS</h3>' +
                                    '<p style="font-size: 16px; margin-top: 5px;">PT. United Tractors Tbk. Cabang Ujung Pandang</p>' +
                                '</div>' +
                                '<hr style="border-top: 2px solid black; margin-top: 15px; margin-bottom: 15px;">' +
                                '<h4 style="font-weight: bold; text-align: center; margin: 0;">Periode Tanggal: ' + teksPeriode + '</h4>' +
                            '</div>'
                        );
                        
                        $(win.document.body).append(
                            '<table class="ttd-box">' +
                                '<tr>' +
                                    '<td>Mengetahui,<br>Service Dept. Head</td>' +
                                    '<td>Disetujui Oleh,<br>Supervisor</td>' +
                                    '<td>Dibuat Oleh,<br>Toolkeeper</td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td class="ttd-space"></td>' +
                                    '<td class="ttd-space"></td>' +
                                    '<td class="ttd-space"></td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td>( ____________________________ )</td>' +
                                    '<td>( ____________________________ )</td>' +
                                    '<td>( ____________________________ )</td>' +
                                '</tr>' +
                            '</table>'
                        );
                    }
                }
            ]
        }); 
    }

    var toggleSwitch = document.querySelector('#darkModeSwitch');
    var body = document.querySelector('body');
    var icon = toggleSwitch ? toggleSwitch.querySelector('i') : null;

    if (localStorage.getItem('theme') === 'dark') {
        body.classList.add('able-dark-mode');
        if(icon) { icon.className = 'feather icon-sun'; }
    } else {
        if(icon) { icon.className = 'feather icon-moon'; }
    }

    if(toggleSwitch) {
        toggleSwitch.addEventListener('click', function (e) {
            e.preventDefault();
            body.classList.toggle('able-dark-mode');
            
            if (body.classList.contains('able-dark-mode')) {
                localStorage.setItem('theme', 'dark');
                if(icon){ icon.className = 'feather icon-sun'; }
            } else {
                localStorage.setItem('theme', 'light');
                if(icon){ icon.className = 'feather icon-moon'; }
            }
        });
    }

    if($('#reservation').length) {
       $('#reservation').daterangepicker({ locale: { format: 'DD/MM/YYYY' } });
    }

    $('a, button').filter(function() {
        return $(this).text().trim().includes('Upgrade To Pro');
    }).parent().remove();
    
  });
</script>
</body>
</html>