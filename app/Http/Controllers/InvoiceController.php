<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Http\Request;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;
use Psy\Command\WhereamiCommand;

class InvoiceController extends Controller
{

    public function invoice($transaction)
    {
        /*A4 width : 219mm*/
        $data  = Transaction::findOrFail($transaction);
        $query = TransactionItem::with('product')->where('transactions_id', $data->id)->get();

        $pdf = new Fpdf('P', 'mm', 'A4');

        $pdf->AddPage();
        /*output the result*/

        /*set font to arial, bold, 14pt*/
        $pdf->SetFont('Arial', 'B', 20);

        /*Cell(width , height , text , border , end line , [align] )*/

        $pdf->Cell(71, 10, '', 0, 0);
        $pdf->Cell(59, 5, 'Invoice', 0, 0);
        $pdf->Cell(59, 10, '', 0, 1);

        $pdf->SetFont('Arial', 'B', 15);
        $pdf->Cell(71, 5, 'Rapunzie Store', 0, 0);
        $pdf->Cell(59, 5, '', 0, 0);
        $pdf->Cell(59, 5, 'Details', 0, 1);

        $pdf->SetFont('Arial', '', 10);

        $pdf->Cell(130, 5, 'Near PENS', 0, 0);
        $pdf->Cell(25, 5, 'Customer ID', 0, 0);
        $pdf->Cell(34, 5, ': '.$data->user->id, 0, 1);

        $pdf->Cell(130, 5, 'Sukolilo, 60111', 0, 0);
        $pdf->Cell(25, 5, 'Invoice Date', 0, 0);
        $pdf->Cell(34, 5, ': '.date_format($data->created_at, 'd/m/Y'), 0, 1);

        $pdf->Cell(130, 5, '', 0, 0);
        $pdf->Cell(25, 5, 'Invoice Series', 0, 0);
        $pdf->Cell(34, 5, ': RPZ'.$data->id , 0, 1);

        $pdf->SetFont('Arial', 'B', 15);
        $pdf->Cell(130, 5, 'Bill To', 0, 0);
        $pdf->Cell(59, 5, '', 0, 1);

        $pdf->SetFont('Arial', '', 13);
        $pdf->Cell(130, 5, $data->name , 0, 1);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(130, 5, $data->address , 0, 1);


        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(189, 10, '', 0, 1);


        $pdf->Cell(50, 10, 'Transaction Items', 0, 1);

        $pdf->SetFont('Arial', 'B', 10);
        /*Heading Of the table*/
        $pdf->Cell(10, 6, 'No', 1, 0, 'C');
        $pdf->Cell(120, 6, 'Item Description', 1, 0, 'C');
        $pdf->Cell(58, 6, 'Price', 1, 1, 'C');/*end of line*/
        /*Heading Of the table end*/
        $pdf->SetFont('Arial', '', 10);
        foreach ($query as $index => $qn) {

            $pdf->Cell(10, 6, $index+1, 1, 0);
            $pdf->Cell(120, 6, $qn->product->name , 1, 0);
            $pdf->Cell(58, 6, number_format($qn->product->price) , 1, 1, 'R');
        }

        $pdf->Cell(118, 6, '', 0, 0);
        $pdf->Cell(25, 6, 'Subtotal', 0, 0);
        $pdf->Cell(45, 6, 'Rp.'.number_format($data->total_price), 1, 1, 'R');


        return $pdf->Output('D', 'RPZ'.$data->id.'-'.date('dmYHis').'.pdf');

    }
}
