<?php
chdir(dirname(__FILE__));
//------------------------------------------------------
//CONFIGURATION
//------------------------------------------------------
require '../config.php';
$reportSubject = "New Agents Intro Email Report (".date("m/d/Y").")";
$reportTo = "eltonf@tpionline.com";


//------------------------------------------------------
//RUNTIME
//------------------------------------------------------

    $agents = pullNewAgents($dbci);
    for($i = 0; $i < sizeof($agents); $i++) {
        mailAgent($agents[$i], "Thank You For Joining! - Annual Conference");
        // mailAgent($agents[$i], "Monthly Fee Charge - Your credit card was declined");

         echo "Email Sent To " .  $agents[$i]->fname . "!";

    }
    sendReport($agents, $reportSubject, $reportTo);

//------------------------------------------------------
//FUNCTIONS
//------------------------------------------------------
function pullNewAgents($dbci) {
    $query = "SELECT  id, tpicode, fname, lname, email, startDateNew FROM agents WHERE (`status` ='active')  AND  SUBDATE(CURDATE(),1)";
    $runtime = $dbci->query($query);
    while($row = $runtime->fetch_assoc()) {
        $object = new stdClass();
        $object->id = $row['id'];
        $object->tpicode = $row['tpicode'];
        $object->fname = $row['fname'];
        $object->lname = $row['lname'];
        $object->email = $row['email'];
        $object->db_startDate = $row['startDateNew'];
        $array[] = $object;
    }
    return $array;
}

function mailAgent($agent, $subject) {
    $header = generateAgentHeader();
    $message = generateMessageBody($agent);
    mail($agent->email, $subject, $message, $header);
}


function generateMessageBody() {
    $message = "<p>Hey there, Rockstar!</p>
                <p>Welcome to the family! We are so happy you joined TPI. Also, perfect timing!</p>
                <p>We are hosting our annual conference TPI Rocks this year in Orlando. As a welcome gift, we are giving all of our new agents a discount code for registration.</p>
                <p>At the moment of check out use Rocks2020 for a $50 reduction. This code can only be used within 30 after joining TPI.</p>
                <p>For more information about TPI Rocks, you can check our awesome website <a href='http://tpirocks.com/'>here</a>!</p>
                <p>Thanks 😊</p>";
    return $message;
}
function generateAgentHeader() {
    $emailHeader = "MIME-Version: 1.0" . "\r\n";
    $emailHeader .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $emailHeader .= 'From: TPI Events <no-reply@tpionline.com>' . "\r\n";
    $emailHeader .= 'Reply-To: events@tpionline.com' . "\r\n";
    $emailHeader .= 'BCC: dennisa@tpionline.com' . "\r\n";
    return $emailHeader;
}

function sendReport($agents, $subject, $to) {
    $header = generateReportHeaders();
    $message = generateReportBody($agents);
    mail($to, $subject, $message, $header);
}

function generateReportHeaders() {
    $emailHeader = "MIME-Version: 1.0" . "\r\n";
    $emailHeader .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $emailHeader .= 'From: The Ghost in the Machine <no-reply@tpionline.com>' . "\r\n";
    return $emailHeader;
}
function generateReportBody($agents) {
    if($agents) {
        // $agents = sortArray($agents);
        $message = "<center><h2>TPI Rocks 2020 Promo Code Email Sent Out To New Agents On: ".date("m/d/Y h:i a")."</h2></center>";
        $message .= "<table width='100%' border='1'>";
        $message .= "<thead><th>TPI Code</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Start Date</th></thead>";
        $message .= "<tbody>";
        for($i = 0; $i < sizeof($agents); $i++) {
            $message .= "<tr>";
                $message .= "<td><center>".$agents[$i]->tpicode."</center></td>";
                $message .= "<td><center>".$agents[$i]->fname."</center></td>";
                $message .= "<td><center>".$agents[$i]->lname."</center></td>";
                $message .= "<td><center>".$agents[$i]->email."</center></td>";
                $message .= "<td><center>".$agents[$i]->db_startDate."</center></td>";
            $message .= "</tr>";
        }
        $message .= "</tbody>";
        $message .= "</table>";

        $message2 = "</br><p>Hey there, Rockstar!</p>
        <p>Welcome to the family! We are so happy you joined TPI. Also, perfect timing!</p>
        <p>We are hosting our annual conference TPI Rocks this year in Orlando. As a welcome gift, we are giving all of our new agents a discount code for registration.</p>
        <p>At the moment of check out use Rocks2020 for a $50 reduction. This code can only be used within 30 after joining TPI.</p>
        <p>For more information about TPI Rocks, you can check our awesome website
        here! 
        </p>
        <p>Thanks 😊</p>";


        $message.="<div><center><h1>Email That Was Sent:</h1></center></br>".$message2."</div>";
    } else {
        $message = "<h3>No New Agents Were Emailed Today</h3>";
    }
    return $message;
}

