<?php
require_once "../../lib/db_function.php";

$dates = explode(" - ", $_GET['date']);
$start_date = (new \DateTime())->format("Y-m-01");
$end_date = (new \DateTime())->format("Y-m-d");
if(!empty($dates[0])){
	$start_date = $dates[0];
}
if(!empty($dates[1])){
	$end_date = $dates[1];
}

$loans = returnAllData($db, $sql = "SELECT a.id,
									a.loan_date,
									a.status,
									a.loan_amount,
									CONCAT(COALESCE(b.fname,''), ' ', COALESCE(b.lname,'')) AS member_name,
									c.lname AS loan_name,
									f.interest,
									f.installment,
									f.saving,
									d.name AS committee_name,
									e.name AS president_name
									FROM member_loans AS a
									INNER JOIN member as b
									ON a.member_id = b.id
									INNER JOIN loan_type AS c
									ON a.loan_id = c.id
									LEFT JOIN users AS d
									ON a.committee = d.id
									LEFT JOIN users AS e
									ON a.president = e.id
									LEFT JOIN member_loan_settings AS f
									ON a.id = f.loan_id
									WHERE a.president_status = ?
									AND a.committee_status = ?
									AND a.loan_date BETWEEN ? AND ?
									", [1, 1, $start_date, $end_date]);

if(count($loans) > 0){
	$data = "";
	$count = 1;
	foreach($loans AS $loan){
		$data .= "<tr>";
			$data .= "<td>".($count++)."</td>";
			$data .= "<td>{$loan['loan_date']}</td>";
			$data .= "<td>{$loan['member_name']}</td>";
			$data .= "<td style='text-align: right;'>".number_format($loan['loan_amount'])."</td>";
			$data .= "<td>{$loan['loan_name']}".($loan['interest']?"[{$loan['interest']}%]":"")."</td>";
			$data .= "<td style='text-align: right;'>".number_format($loan['loan_amount'] * $loan['interest']/100) ."</td>";
			$data .= "<td style='text-align: right;'>".number_format($loan['installment']) ."</td>";
			$data .= "<td style='text-align: right;'>".number_format($loan['saving']) ."</td>";
			$data .= "<td>{$loan['committee_name']}</td>";
			$data .= "<td>{$loan['president_name']}</td>";
		$data .= "</tr>";
	}
} else {
	$data = "<tr><td colspan='8' style='text-align: center'>No Loan in the selected range {$_GET['date']}</td></tr>";
}

$today = (new \DateTime())->format("F jS Y");
$content = <<<PDF_DATA
<html>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
	<head>
		<title>Loan Provided {$_GET['date']}</title>
	</head>
	<body>
		<h3>
			<i>
				IKIMINA ABASARE GROUP<br />
				C/O RWANDA POLYTECHNIC / IPRC GISHARI<br />
				P.O BOx: 60 RWAMAGANA
			</i>
		</h3>
		<h4 style='text-align: center; text-decoration: underline'>Provided Loans: {$_GET['date']}</h4>

		<table style="width: 100%; border-collapse: collapse;" border=1>
			<thead>
				<tr>
					<th>#</th>
					<th>Date</th>
					<th>Member</th>
					<th>Loan Amount</th>
					<th>Type</th>
					<th>Interest</th>
					<th>Installment</th>
					<th>Min Saving</th>
					<th>Committee</th>
					<th>President</th>
				</tr>
			</thead>
			<tbody>
				{$data}
			</tbody>
		</table>
		<br />&nbsp;
		<br />&nbsp;
		<br />
		Prerpared By:<br />
		{$_SESSION['user']['name']}<br />
		{$today}

		<style>
		table{
			font-size: 12px;
			font-family: helvetica;
		}
		</style>
	</body>
</html>
PDF_DATA;
// var_dump("<pre>", $_SESSION, "</pre>"); die();
// echo $content; die();
use Dompdf\Dompdf;

$dompdf = new Dompdf();


$dompdf->loadHtml($content);

$dompdf->setPaper('A4', 'landscape');

$dompdf->render();

$dompdf->stream("approved_loans.pdf");