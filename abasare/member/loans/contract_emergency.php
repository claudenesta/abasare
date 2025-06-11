<?php

require_once "../../lib/db_function.php";
use NumberToWords\NumberToWords;

$loan = first($db, "SELECT 	a.id,
							b.fname,
							b.lname,
							b.company,
							b.id_number,
							b.id_location,
							c.name AS village_name,
							d.name AS cell_name,
							e.name AS sector_name,
							f.name AS district_name,
							a.loan_amount,
							COALESCE(h.interest, i.interest) AS interest,
							COALESCE(h.terms, i.terms) AS terms,
							h.frequency,
							a.member_id,
							a.loan_date,

							l.name AS signatory_1_name,
							l.signature AS signatory_1_signature,
							n.id_number AS signatory_1_id_card,
							n.company AS signatory_1_company,

							m.name AS signatory_2_name,
							m.signature AS signatory_2_signature,
							o.id_number AS signatory_2_id_card,
							o.company AS signatory_2_company,

							p.signature AS member_signature,

							a.committee_status,
							a.committee_date,
							j.name AS committee_name,
							j.signature AS committee_signature,
							a.committee_reject_comment,

							a.president_date,
							k.name AS president_name,
							k.signature as president_signature,

							COALESCE(q.approved_amount, a.loan_amount) AS approved_amount

							FROM member_loans AS a
							INNER JOIN member AS b
							ON a.member_id = b.id
							INNER JOIN villages AS c
							ON b.village_id = c.id
							INNER JOIN cells AS d
							ON c.cell_id = d.id
							INNER JOIN sectors AS e
							ON d.sector_id = e.id
							INNER JOIN districts AS f
							ON e.district_id = f.id
							LEFT JOIN member_loan_settings AS h
							ON a.id = h.loan_id
							INNER JOIN loan_type AS i
							ON a.loan_id = i.id
							INNER JOIN users AS j
							ON a.committee = j.id
							INNER JOIN users AS k
							ON a.president = k.id
							INNER JOIN users AS l
							ON a.signatory_1 = l.id
							INNER JOIN member AS n
							ON l.member_acc = n.id
							INNER JOIN users AS m
							ON a.signatory_2 = m.id
							INNER JOIN member AS o
							ON m.member_acc = o.id
							INNER JOIN users as p
							ON b.id = p.member_acc
							LEFT JOIN emergency_details AS q
							ON a.id = q.loan_id
							WHERE a.id = ?
							", [$_GET['loan_id']]);

$member_name = "<b>".$loan['fname']." ".$loan['lname']."</b>";

$loan_amount = NumberToWords::transformNumber("en", $loan['loan_amount'])." (mu nyuguti) ".number_format($loan['loan_amount'])." RWF (mu mibare)";

$interest_amount = number_format($loan['approved_amount']*$loan['interest']/100)."";

$main_path = $_SERVER['DOCUMENT_ROOT'];
$committee_signature = "";
if(!is_null($loan['committee_signature'])){
	$signature_path = $main_path.DIRECTORY_SEPARATOR.$loan['committee_signature'];

	$committee_signature = '<img style="height: 29px; margin-top: 10px;" src="'.getDataURI($signature_path).'" alt="">';
}

$member_signature = "";
if(!is_null($loan['member_signature'])){
	$signature_path = $main_path.DIRECTORY_SEPARATOR.$loan['member_signature'];
	$member_signature = '<img style="height: 29px; margin-top: 10px;" src="'.getDataURI($signature_path).'" alt="">';
}

$loan_committee_decision = "";

if($loan['committee_status'] == 1){
	$loan_committee_decision = "Arayemerewe: <b>YEGO</b>". str_repeat("&nbsp;", 30) ." Ayo yemerewe: <b>".number_format($loan['approved_amount'])." RWF</b>";
} else {
	$loan_committee_decision = "Ntabwo ayemerewe: <b>YEGI</b>". str_repeat("&nbsp;", 30) ." Impamvu:<b>{$loan['committee_reject_comment']}</b>";
}


$president_signature = "";
if(!is_null($loan['president_signature'])){
	$signature_path = $main_path.DIRECTORY_SEPARATOR.$loan['president_signature'];
	$president_signature = '<img style="height: 29px; margin-top: 10px;" src="'.getDataURI($signature_path).'" alt="">';
}

$stamp_path = $main_path.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."signatures".DIRECTORY_SEPARATOR."stamp.png";
$stamp = '<img style="width: 100px; margin-left: -40px; margin-top: -60px" src="'.getDataURI($stamp_path).'" alt="">';


$content = <<<HTML_DATA
<html>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
	<head>
		<title>Loan Contract - Emergency</title>
		<style>
		p {
			font-size: 14px;
			text-align: justify;
		}
		h2 {
			text-decoration: underline;
			font-size: 14px;
			font-weight: bold;
		}
		h3 {
			font-size: 16px;
		}
		</style>
	</head>
	<body>
		<h3>
			ISANDUKU Y UBWISUNGANE « ABASARE GROUP » <br />
			C/O RWANDA POLYTECHNIC / IPRC GISHARI<br />
			P.O BOx: 60 RWAMAGANA<br />
			E-mail: abasaregroup@gmail.com<br />
		</h3>
		<h2 style='text-align: center'>
			INYANDIKO YOGUSABA INGUZANYO YI NGOBOKA ID: {$loan['id']}
		</h2>

		<p>
			Njyewe {$member_name}<br />
			Umunyamuryango wisanduku «ABASARE GROUP» ufite indangamuntu nomero <b>{$loan['id_number']}</b> yatangiwe <b>{$loan['id_location']}</b> utuye mu Mudugudu wa <b>{$loan['village_name']}</b> Akagari ka <b>{$loan['cell_name']}</b> Umurenge wa <b>{$loan['sector_name']}</b> Akarere ka <b>{$loan['district_name']}</b> ndasaba inguzanyo y’ingoboka ingana n’amafaranga y’u Rwanda <b>{$loan_amount}</b> nzishyura mugihe cy’ukwezi uhereye igihe nyiherewe, nkayihabwa hakuwemo amafaranga y’u Rwanda <b>{$interest_amount}</b> y’umufuragiro nkuko biteganywa mungingo ya 17 y’amategeko shingiro agenga ISANDUKU Y’UBWISUNGANE «ABASARE GROUP»
		</p>

		Italiki <b>{$loan['loan_date']}</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Umukono: {$member_signature}<br />
		&nbsp;<br />
		<h4>UMWANZURO WAKANAMA GASHINZWE INGUZANYO</h4>
		{$loan_committee_decision}<br />
		&nbsp;<br />
		Amazina: <b>{$loan['committee_name']}</b><br />
		Italiki: <b>{$loan['committee_date']}</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Umukono: {$committee_signature}<br /><br />
		&nbsp;<br />
		<h4>UHAGARARIYE ISANDUKU</h4>
		Amazina: <b>{$loan['president_name']}</b><br />
		Umurimo ashinzwe: <b></b><br />
		&nbsp;<br />
		Uhagarariye isanduku<br />
		Italiki: <b>{$loan['president_date']}</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Umukono + Cachet: {$president_signature}{$stamp}<br /><br />
	</body>
</html>
HTML_DATA;

// echo $content; die();

use Dompdf\Dompdf;

$dompdf = new Dompdf();


$dompdf->loadHtml($content);

$dompdf->setPaper('A4', 'portrait');

$dompdf->render();

$dompdf->stream("Emergency-Contract.pdf");