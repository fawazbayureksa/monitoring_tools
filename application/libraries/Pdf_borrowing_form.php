<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!class_exists('FPDF')) {
    if (file_exists(APPPATH . 'third_party/fpdf/fpdf.php')) {
        require_once APPPATH . 'third_party/fpdf/fpdf.php';
    } elseif (file_exists(FCPATH . 'vendor/autoload.php')) {
        require_once FCPATH . 'vendor/autoload.php';
    }
}

class Pdf_borrowing_form extends FPDF {

    protected $request;
    protected $items;
    protected $wNo = 12;
    protected $wName = 74;
    protected $wBorrow = 32;
    protected $wReturn = 32;
    protected $wStatus = 30;

    public function __construct($params = []) {
        parent::__construct('P', 'mm', 'A4');
        $this->request = isset($params['request']) ? $params['request'] : [];
        $this->items   = isset($params['items']) ? $params['items'] : [];
        $this->SetMargins(15, 10, 15);
        $this->SetAutoPageBreak(true, 15);
        $this->AliasNbPages('{nb}');
    }

    public function set_data($request, $items) {
        $this->request = $request;
        $this->items = $items;
    }

    function Header() {
        if ($this->PageNo() == 1) {
            // 1. Top-Left Yellow Block
            $this->SetFillColor(253, 184, 19); // UT Yellow #FDB813
            $this->Rect(15, 10, 14, 22, 'F');

            // 2. Logo UT Circular Mark
            $logoPath = FCPATH . 'assets/dist/img/logoUT.png';
            if (file_exists($logoPath)) {
                $this->Image($logoPath, 32, 12, 17, 17);
            }

            // 3. United Tractors text next to logo
            $this->SetXY(52, 13);
            $this->SetFont('Arial', 'B', 15);
            $this->SetTextColor(30, 30, 30);
            $this->Cell(80, 6, 'UNITED TRACTORS', 0, 1, 'L');

            $this->SetX(52);
            $this->SetFont('Arial', '', 9);
            $this->SetTextColor(110, 110, 110);
            $this->Write(5, 'member of ');
            $this->SetFont('Arial', 'B', 9);
            $this->SetTextColor(0, 82, 155); // Astra Blue
            $this->Write(5, 'ASTRA');

            // 4. Top-Right 3 Yellow Bars
            $this->SetFillColor(253, 184, 19);
            $this->Rect(155, 11, 40, 3.2, 'F');
            $this->Rect(155, 16.5, 40, 3.2, 'F');
            $this->Rect(155, 22, 40, 3.2, 'F');

            // 5. Company Address Block
            $this->SetXY(15, 36);
            $this->SetFont('Arial', '', 9.5);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(0, 4.5, 'PT UNITED TRACTORS Tbk', 0, 1, 'L');
            $this->Cell(0, 4.5, 'Jalan raya Bekasi Km. 22,', 0, 1, 'L');
            $this->Cell(0, 4.5, 'Jakarta 13910 - Indonesia', 0, 1, 'L');
            $this->Cell(0, 4.5, 'Monitoring Tools', 0, 1, 'L');

            // 6. Title: FORM PEMINJAMAN TOOLS
            $this->Ln(6);
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 5, 'FORM PEMINJAMAN TOOLS', 0, 1, 'C');
            $this->SetFont('Arial', '', 11);
            $this->Cell(0, 5, 'Cabang Ujung Pandang', 0, 1, 'C');

            // Horizontal Dividing Line below title
            $this->Ln(2);
            $this->SetDrawColor(0, 0, 0);
            $this->SetLineWidth(0.6);
            $this->Line(15, $this->GetY(), 195, $this->GetY());
            $this->Ln(8);
        } else {
            // Subsequent Page Header
            $this->SetFont('Arial', 'I', 8.5);
            $this->SetTextColor(120, 120, 120);
            $reqNum = isset($this->request['request_number']) ? $this->request['request_number'] : '';
            $this->Cell(0, 5, 'FORM PEMINJAMAN TOOLS - Cabang Ujung Pandang (Lanjutan) - ' . $reqNum, 0, 1, 'R');
            $this->Ln(2);
            $this->SetTextColor(0, 0, 0);
            $this->renderTableHeader();
        }
    }

    public function renderTableHeader() {
        $this->SetFont('Arial', 'B', 9.5);
        $this->SetLineWidth(0.3);
        $this->SetDrawColor(0, 0, 0);

        $this->Cell($this->wNo, 7, 'No.', 1, 0, 'C');
        $this->Cell($this->wName, 7, 'Tools Name', 1, 0, 'C');
        $this->Cell($this->wBorrow, 7, 'Borrow Date', 1, 0, 'C');
        $this->Cell($this->wReturn, 7, 'Return Date', 1, 0, 'C');
        $this->Cell($this->wStatus, 7, 'Status', 1, 1, 'C');
    }

    function Footer() {
        $this->SetY(-12);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(120, 120, 120);
        $this->Cell(0, 6, 'Halaman ' . $this->PageNo() . ' dari {nb}', 0, 0, 'C');
    }

    protected function format_indo_date($datetime, $with_time = false) {
        if (empty($datetime) || $datetime == '0000-00-00 00:00:00' || $datetime == '0000-00-00') {
            return '-';
        }
        $months = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        $time = strtotime($datetime);
        if (!$time) return '-';

        $day = date('d', $time);
        $month = $months[(int)date('m', $time)];
        $year = date('Y', $time);

        $result = $day . ' ' . $month . ' ' . $year;
        if ($with_time) {
            $result .= ' ' . date('H:i', $time);
        }
        return $result;
    }

    protected function format_short_date($datetime) {
        if (empty($datetime) || $datetime == '0000-00-00 00:00:00' || $datetime == '0000-00-00') {
            return '-';
        }
        $time = strtotime($datetime);
        return $time ? date('d M Y', $time) : '-';
    }

    public function generate_pdf($output_mode = 'I', $filename = 'document.pdf') {
        $this->AddPage();

        // 7. Request Information Block
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(0, 0, 0);
        $labelWidth = 35;
        $colonWidth = 5;
        $lineHeight = 5.5;

        $reqNum = isset($this->request['request_number']) ? $this->request['request_number'] : '-';
        $reqDate = isset($this->request['created_at']) ? $this->format_indo_date($this->request['created_at']) : $this->format_indo_date(date('Y-m-d'));
        
        $borrower = '';
        if (!empty($this->request['borrower_name'])) {
            $borrower .= $this->request['borrower_name'];
        }
        if (!empty($this->request['borrower_nrp'])) {
            $borrower .= ' - ' . $this->request['borrower_nrp'];
        }
        if (empty($borrower)) {
            $borrower = '-';
        }

        $location = isset($this->request['site_location']) ? $this->request['site_location'] : '-';

        $info = [
            'No. Request'     => $reqNum,
            'Date'            => $reqDate,
            'Borrower & NRP'  => $borrower,
            'Location'        => $location,
        ];

        foreach ($info as $label => $val) {
            $this->Cell($labelWidth, $lineHeight, $label, 0, 0, 'L');
            $this->Cell($colonWidth, $lineHeight, ':', 0, 0, 'C');
            $this->SetFont('Arial', ($label == 'No. Request' ? 'B' : ''), 10);
            $this->Cell(0, $lineHeight, $val, 0, 1, 'L');
            $this->SetFont('Arial', '', 10);
        }

        $this->Ln(6);

        // 8. Borrowed Tools Table
        $this->renderTableHeader();

        $this->SetFont('Arial', '', 9.5);
        $bDate = isset($this->request['borrow_date']) ? $this->format_short_date($this->request['borrow_date']) : '-';
        $rDate = isset($this->request['return_date_actual']) ? $this->format_short_date($this->request['return_date_actual']) : (isset($this->request['return_date_est']) ? $this->format_short_date($this->request['return_date_est']) : '-');

        $rowNo = 1;
        $items = !empty($this->items) ? $this->items : [];

        // Fallback jika array items kosong namun request memiliki tool_id
        if (empty($items) && !empty($this->request['tool_name'])) {
            $items[] = [
                'tool_name'        => $this->request['tool_name'],
                'part_number'      => isset($this->request['part_number']) ? $this->request['part_number'] : '',
                'qty_requested'    => isset($this->request['qty_requested']) ? $this->request['qty_requested'] : 1,
                'return_condition' => isset($this->request['return_condition']) ? $this->request['return_condition'] : null
            ];
        }

        foreach ($items as $item) {
            $toolText = isset($item['tool_name']) ? $item['tool_name'] : 'Tool';
            if (!empty($item['part_number'])) {
                $toolText .= ' (' . $item['part_number'] . ')';
            }
            if (!empty($item['qty_requested']) && $item['qty_requested'] > 1) {
                $toolText .= ' (x' . $item['qty_requested'] . ')';
            }

            // Status label mapping (Do not default to 'Baik' when inspection data is missing)
            $statusLabel = '-';
            if (!empty($item['return_condition'])) {
                if ($item['return_condition'] === 'broken') $statusLabel = 'Rusak';
                elseif ($item['return_condition'] === 'lost') $statusLabel = 'Hilang';
                elseif ($item['return_condition'] === 'good') $statusLabel = 'Baik';
            } elseif (!empty($this->request['status'])) {
                if ($this->request['status'] === 'Completed') $statusLabel = 'Baik';
                elseif (strpos($this->request['status'], 'RUSAK') !== false) $statusLabel = 'Rusak';
                elseif (strpos($this->request['status'], 'HILANG') !== false) $statusLabel = 'Hilang';
                elseif ($this->request['status'] === 'BAKK') $statusLabel = 'BAKK';
            }

            // Truncate tool text if it exceeds cell width
            while ($this->GetStringWidth($toolText) > ($this->wName - 4) && strlen($toolText) > 10) {
                $toolText = substr($toolText, 0, -4) . '...';
            }

            $this->Cell($this->wNo, 6.5, $rowNo . '.', 1, 0, 'C');
            $this->Cell($this->wName, 6.5, ' ' . $toolText, 1, 0, 'L');
            $this->Cell($this->wBorrow, 6.5, $bDate, 1, 0, 'C');
            $this->Cell($this->wReturn, 6.5, $rDate, 1, 0, 'C');
            $this->Cell($this->wStatus, 6.5, $statusLabel, 1, 1, 'C');
            $rowNo++;
        }

        $this->Ln(7);

        // Check if there is enough space on this page for Statement & Signatures (~60mm)
        // If not, break to new page
        if ($this->GetY() > 215) {
            $this->AddPage();
        }

        // 9. Responsibility Statement
        $this->SetFont('Arial', '', 9.5);
        $statement = 'Pihak peminjam bertanggung jawab menjaga tools yang dipinjam dan mengembalikan tools kembali dalam kondisi baik.';
        $this->MultiCell(0, 5, $statement, 0, 'L');

        // 10. Approval & Signatures Section
        $this->Ln(16);
        $colW = 60; // 3 columns x 60mm = 180mm

        $this->SetFont('Arial', '', 10);
        $this->Cell($colW, 5, 'Dibuat Oleh', 0, 0, 'C');
        $this->Cell($colW, 5, 'Diperiksa Oleh', 0, 0, 'C');
        $this->Cell($colW, 5, 'Disetujui Oleh', 0, 1, 'C');

        $this->Cell($colW, 5, 'Mechanic Leader', 0, 0, 'C');
        $this->Cell($colW, 5, 'Tools Keeper', 0, 0, 'C');
        $this->Cell($colW, 5, 'Supervisor', 0, 1, 'C');

        // Blank space for physical signature
        $this->Ln(24);

        // Signature bracket parentheses with underline ( ___________ )
        $bracketWidth = 46;
        $yBracket = $this->GetY();
        for ($i = 0; $i < 3; $i++) {
            $xStart = 15 + ($i * $colW) + (($colW - $bracketWidth) / 2);
            $xEnd = $xStart + $bracketWidth;

            $this->SetFont('Arial', '', 14);
            $this->Text($xStart - 1.5, $yBracket + 0.8, '(');
            $this->Line($xStart + 0.5, $yBracket, $xEnd - 0.5, $yBracket);
            $this->Text($xEnd - 1, $yBracket + 0.8, ')');
        }

        // Output PDF
        $this->Output($output_mode, $filename);
    }
}
