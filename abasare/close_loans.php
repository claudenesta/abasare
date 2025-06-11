<?php
    require_once("db_function.php");
    $db->beginTransaction();
    try{
        $data = returnAllData($db, "SELECT  a.loan_amount,
                                            SUM(b.amount) AS paid_amount,
                                            a.id
                                            FROM member_loans as a 
                                            INNER JOIN lend_payments AS b
                                            ON a.id = b.borrower_loan_id
                                            WHERE a.status = ? 
                                            GROUP BY a.id", ['ACTIVE']);
        $counter = 0;
        $closed_loans = [];
        foreach($data AS $loan){
            if($loan['loan_amount'] <= $loan['paid_amount']){
                saveData($db, "UPDATE member_loans SET status='CLOSED' WHERE id='{$loan['id']}'" );
                $closed_loans[] = $loan['id'];
                $counter++;
            }
        }
        $db->commit();

        echo sprintf("%s were open and now they are closed!<br />IDs: %s", $counter, implode($closed_loans));
    } catch(\Exception $e){
        $db->rollback();
        throw new \Exception($e->getMessage());
    }