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

							l.name AS signatory_1_name,
							l.signature AS signatory_1_signature,
							n.id_number AS signatory_1_id_card,
							n.company AS signatory_1_company,

							m.name AS signatory_2_name,
							m.signature AS signatory_2_signature,
							o.id_number AS signatory_2_id_card,
							o.company AS signatory_2_company,

							p.signature AS member_signature,

							a.committee_date,
							j.name AS committee_name,
							j.signature AS committee_signature,

							a.president_date,
							k.name AS president_name,
							k.signature as president_signature

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
							WHERE a.id = ?
							", [$_GET['loan_id']]);
// Get the last Saving of before the loan approval

$contributions = returnAllData($db, "SELECT * 
											FROM (
												SELECT 	a.sav_amount AS amount,
														a.year, a.month,
														CONCAT(a.year, '-', IF(a.month < 10, '0',''), a.month, '-01') AS contribution_month
														FROM saving AS a
														WHERE a.member_id = ?
														ORDER BY id DESC
											) AS a
											WHERE a.contribution_month <= ?
											ORDER BY contribution_month DESC
											LIMIT 0,3
											", $condi = [$loan['member_id'], (new \DateTime($loan['president_date']))->format('Y-m-01')]);
// var_dump("<pre>", $contributions, $condi, $loan['member_id']); die();
$average = 0;
foreach($contributions AS $single_contribution){
	$average += $single_contribution['amount'];
}

$average /= 3;
$contribution = "<b>".number_format($average)." RWF</b>";
//Get Payment installement informant
$date_object = new \DateTime($loan['president_date']);
if($date_object->format('j') <= 15){
	$first_installement_month = "<b>". $date_object->format("m, Y")."</b>";
} else {
	$date_object->modify("+1 month");
	$first_installement_month = "<b>". $date_object->format("m, Y")."</b>";
}

$date_object->modify("+".$loan['terms']." months");
$last_installement_month = "<b>". $date_object->format("m, Y")."</b>";
// create the html content required for contract preparation
$ikimina = "<b>IKIMINA ABASARE GROUP</b>";
$ugurizwa = "<b>Nyir ukugurizwa</b>";
$title = "Bwana/Madamu";
$member_name = "<b>".$loan['fname']." ".$loan['lname']."</b>";
$company = "<b>{$loan['company']}</b>";

$loan_amount = NumberToWords::transformNumber("en", $loan['loan_amount'])." ".number_format($loan['loan_amount'])." RWF";

$interest_rate = $loan['interest'];
$period = $loan['terms'];
$installment_amount = number_format($loan['loan_amount']/$loan['terms'])." RWF";

$loand_approved_at = "<b>{$loan['president_date']}</b>";

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


$signatory_1_signature = "";
if(!is_null($loan['signatory_1_signature'])){
	$signature_path = $main_path.DIRECTORY_SEPARATOR.$loan['signatory_1_signature'];
	$signatory_1_signature = '<img style="height: 29px; margin-top: 10px;" src="'.getDataURI($signature_path).'" alt="">';
}

$signatory_2_signature = "";
if(!is_null($loan['signatory_2_signature'])){
	$signature_path = $main_path.DIRECTORY_SEPARATOR.$loan['signatory_2_signature'];
	$signatory_2_signature = '<img style="height: 29px; margin-top: 10px;" src="'.getDataURI($signature_path).'" alt="">';
}

$president_name = $loan['president_name'];
$president_title = "";
$president_signature = "";
if(!is_null($loan['president_signature'])){
	$signature_path = $main_path.DIRECTORY_SEPARATOR.$loan['president_signature'];
	$president_signature = '<img style="height: 29px; margin-top: 10px;" src="'.getDataURI($signature_path).'" alt="">';
}

$stamp_path = $main_path.DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."signatures".DIRECTORY_SEPARATOR."stamp.png";
$stamp = '<img style="width: 100px; margin-left: -40px; margin-top: -60px" src="'.getDataURI($stamp_path).'" alt="">';

// var_dump("<pre>", $committee_signature, $member_signature, "</pre>"); die();

$content = <<<HTML_DATA
<html>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
	<head>
		<title>Loan Contract</title>
		<style>
		p {
			font-size: 14px;
			text-align: justify;
		}
		</style>
	</head>
	<body>
		<h3>
			<i>
				IKIMINA ABASARE GROUP<br />
				C/O RWANDA POLYTECHNIC / IPRC GISHARI<br />
				P.O BOx: 60 RWAMAGANA
			</i>
		</h3>

		<h2 style='text-align: center'>AMASEZERANO YO KUGURIZWA ID<sup><u>o</u></sup>: {$loan['id']}</h2>

		Hagati ya:
		<p><strong>IKIMINA ABASARE GROUP</strong> ifite icyicaro gikuru mu:<br />
		Mudugudu wa........<strong>SHABURONDO</strong>.......<br />
		Akagari ka.........<strong>BWINSANGA</strong>........<br />
		Umurenge wa .......<strong>GISHARI</strong>..........<br />
		Akarere ka ........<strong>RWAMAGANA</strong>....... ,<br />
		ihagarariwe na Perezida wayo ariyo yitwa <strong>«{$ikimina}»</strong> muri aya masezerano</p>
		Na

		<p>{$title} {$member_name}<br />
		Ufite indangamuntu nomero: <b>{$loan['id_number']}</b><br />
		Yatangiwe: <b>{$loan['id_location']}</b><br />
		Utuye mu Mudugudu wa: <b>{$loan['village_name']}</b><br />
		Akagari ka: <b>{$loan['cell_name']}</b><br />
		Umurenge wa: <b>{$loan['sector_name']}</b><br />
		Akarere ka: <b>{$loan['district_name']}</b><br />
		umunyamuryango w <strong>«{$ikimina}»</strong> ariwe witwa <strong>«{$ugurizwa}»</strong></p>

		<strong>Ingingo ya 1:</strong>

		<p><strong>{$title} {$member_name}</strong> , Umukozi wa <strong>{$company}</strong> ahawe inguzanyo y amafaranga yu Rwanda 
		<strong>{$loan_amount}</strong>.<br />
		{$ugurizwa} yemeye kuyihabwa hakuwemo umufuragiro wa {$interest_rate}% y'umwenda remezo.
		<br />Inguzanyo yose akazayishyura mu mezi ({$period}) yishyura amafaranga yu Rwanda {$installment_amount} buri kwezi.<br />
		Yiyemeje kandi kujya azigama amafaranga atari munsi ya {$contribution} buri kwezi.</p>

		<strong>Ingingo ya 2:</strong>

		<p>{$ugurizwa} yiyemeje kwishyura umwenda wose (uko wavuzwe mu ngingo ya mbere) guhera ku
		mushahara w ukwezi kwa {$first_installement_month}
		kugera ku mushahara w ukwezi kwa {$last_installement_month}
		</p>

		<strong>Ingingo ya 3:</strong>
		<p>{$ugurizwa} yiyemeje kwishyura inguzanyo yasabye yubahiriza amategeko shingiro agenga {$ikimina} cyane cyane mu ngingo ya 11, 12, niya 13.
		</p>

		<strong>Ingingo ya 4:</strong>
		<p>{$ugurizwa} ahaye uburenganzira {$ikimina} bwo gufatira umugabane fatizo
		nubwizigame bye igihe atubahirije aya masezerano. Igihe umugabane fatizo nubwizigame bye bidahagije,
		{$ugurizwa} azakomeza kwishyura umwenda wose kugeza urangiye.</p>

		<strong>Ingingo ya 5:</strong>
		<p>Impaka cyangwa ibibazo byose byavuka ku kutubahiriza aya masezerano, byakemurwa mu bwumvikane bw
		abayagiranye. Byananirana, bigakemurwa hakurijwe amategeko ariho mu Rwanda yerekeranye no gukemura
		impaka. Habonetse ikibazo kitari mu masezerano yavuzwe haruguru, cyaganirwaho, byananirana hakitabazwa
		ubuyobozi bwa Leta cyangwa inkiko zo mu gihugu zibifitiye ububasha.</p>

		<strong>Ingingo ya 6:</strong>
		<p>Aya masezerano yizweho neza n’akanama gashinzwe gutanga inguzanyo kagendeye ku mategeko na amabwiriza
		agenga ikimina abasare group. Uhagarariye akanama gashinzwe gutanga inguzanyo “Credit Committee”</p>
		Amazina :<b>{$loan['committee_name']}</b><br />
		Umukono :{$committee_signature}<br />
		Date :<b>{$loan['committee_date']}</b><br />&nbsp;<br />

		<strong>Ingingo ya 7:Abishingizi</strong>
		<p>Uretse umunyamuryango usaba inguzanyo itarengeje umugabane fatizo n’ubwizigame bwe, umunyamuryango
		usabye inguzanyo isanzwe yishingirwa n’abanyamuryango babiri bakiyemeza kwibutsa uwo bishingiye
		kubahiriza amasezerano no kumwishyurira igihe yananiwe kubahiraza amasezerano. Umunyamuryango ubuze
		abishingizi yemerewe kugurizwa amafaranga atarengeje ubwizigame bwa buri kwezi agejejeho hiyongereyeho
		umugabane fatizo. Nta munyamuryango wemerewe kwishingira abantu barenga babiri (2) mu mwaka umwe.
		Umunyamuryango wishingiye mugenzi we iyo asezeye, ikimina kimuha ibyo kimugomba ari uko uwo
		yishingiye azanye umwishingizi umusimbura. Umwishingizi utishyuriye uwo yishingiye mu gihe kingana
		n’amezi atatu (3) akurikirana abarwa uhereye igihe uwo yishingiye bigaragara ko yananiwe kwiyishyurira,
		umugabane shingiro we cyangwa ubwizigame bwe buri mu kimina birafatirwa kugirango hishyurwe umwenda
		wari usigayemo.</p>

		<strong>Ingingo ya 8:
		<p>Aya masezerano atangiye gushyirwa mu bikorwa uhereye igihe ashyiriweho umukono n impande zombi, ku
		itariki ya {$loand_approved_at} </p>

		<table style="width: 100%">
			<tr>
				<td style="width: 50%">{$ugurizwa}</td>
				<td>{$ikimina}</td>
			</tr>
			<tr>
				<td>Amazina: {$member_name}</td>
				<td>Amazina: <b>{$president_name}</b></td>
			</tr>
			<tr>
				<td>Umukozi wa: {$company}</td>
				<td>Umurimo akora: <b>{$president_title}</b></td>
			</tr>
			<tr>
				<td>Umukono: {$member_signature}</td>
				<td>Umukono + kashe: {$president_signature} {$stamp}</td>
			</tr>
		</table>
		<br />&nbsp;<br />

		<strong>Umwishingizi wa 1:</strong><br />&nbsp;<br />
		Amazina: <b>{$loan['signatory_1_name']}</b><br />
		Ikigo akorera: <b>{$loan['signatory_1_company']}</b><br />
		Nomero y indangamuntu:<b>{$loan['signatory_1_id_card']}</b><br />
		Umukono: {$signatory_1_signature}<br />
		<br />&nbsp;<br />

		<strong>Umwishingizi wa 2:</strong><br />&nbsp;<br />
		Amazina: <b>{$loan['signatory_2_name']}</b><br />
		Ikigo akorera: <b>{$loan['signatory_2_company']}</b><br />
		Nomero y indangamuntu: <b>{$loan['signatory_2_id_card']}</b><br />
		Umukono: {$signatory_2_signature}<br />

	</body>
</html>
HTML_DATA;


// echo $content; die();

use Dompdf\Dompdf;

$dompdf = new Dompdf();


$dompdf->loadHtml($content);

$dompdf->setPaper('A4', 'portrait');

$dompdf->render();

$dompdf->stream("Contract.pdf");