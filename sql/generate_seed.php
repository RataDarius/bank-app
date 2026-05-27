<?php
// Generates INSERT statements for 20 clients under manager1, 15 under manager2
$pwHash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
$now = new DateTime();
$seed = 42;
mt_srand($seed);

function randomDate(DateTime $start, DateTime $end): string {
    $diff = $end->getTimestamp() - $start->getTimestamp();
    $ts = $start->getTimestamp() + mt_rand(0, $diff);
    return date('Y-m-d H:i:s', $ts);
}

echo "-- ===== AUTO-GENERATED SEED DATA =====\n\n";

// Manager3
echo "INSERT INTO users (username, password_hash, role, full_name, email, phone, created_at) VALUES\n";
echo "('manager3', '$pwHash', 'manager', 'Emily Davis', 'emily.davis@securebank.local', '+1-555-0106', '2023-11-01 10:00:00');\n\n";

// 20 clients for manager1 (id=2)
$m1Start = new DateTime('2023-12-01');
$m1End = new DateTime('2024-06-30');
$clientNames1 = [
    'Oliver', 'Emma', 'Liam', 'Sophia', 'Noah', 'Isabella', 'James', 'Mia', 'Lucas', 'Charlotte',
    'Henry', 'Amelia', 'Alexander', 'Harper', 'Daniel', 'Evelyn', 'Matthew', 'Abigail', 'Jackson', 'Ella'
];
$clientUsers1 = [];
echo "INSERT INTO users (username, password_hash, role, full_name, email, phone, created_at) VALUES\n";
for ($i = 0; $i < 20; $i++) {
    $name = $clientNames1[$i];
    $uname = strtolower($name) . '_m1';
    $email = "$uname@email.com";
    $phone = '+1-555-' . str_pad((string)(200 + $i), 4, '0', STR_PAD_LEFT);
    $date = randomDate($m1Start, $m1End);
    $comma = ($i < 19) ? ',' : ';';
    echo "('$uname', '$pwHash', 'client', '$name Williams', '$email', '$phone', '$date')$comma\n";
    $clientUsers1[] = $uname;
}
echo "\n";

// 15 clients for manager2 (id=3)
$m2Start = new DateTime('2024-01-01');
$m2End = new DateTime('2024-08-31');
$clientNames2 = [
    'Aiden', 'Scarlett', 'Sebastian', 'Grace', 'Benjamin', 'Chloe', 'Samuel', 'Zoey', 'Joseph', 'Penelope',
    'David', 'Riley', 'John', 'Lily', 'Owen'
];
$clientUsers2 = [];
echo "INSERT INTO users (username, password_hash, role, full_name, email, phone, created_at) VALUES\n";
for ($i = 0; $i < 15; $i++) {
    $name = $clientNames2[$i];
    $uname = strtolower($name) . '_m2';
    $email = "$uname@email.com";
    $phone = '+1-555-' . str_pad((string)(300 + $i), 4, '0', STR_PAD_LEFT);
    $date = randomDate($m2Start, $m2End);
    $comma = ($i < 14) ? ',' : ';';
    echo "('$uname', '$pwHash', 'client', '$name Johnson', '$email', '$phone', '$date')$comma\n";
    $clientUsers2[] = $uname;
}
echo "\n";

// Get user IDs via a temporary mapping
echo "-- User ID mapping (approximate):\n";
echo "-- admin=1, manager1=2, manager2=3, alice=4, bob=5, charlie=6, manager3=7\n";
echo "-- m1 clients: 8-27, m2 clients: 28-42\n\n";

// Manager clients assignments
echo "INSERT INTO manager_clients (manager_id, client_user_id) VALUES\n";
for ($i = 0; $i < 20; $i++) {
    $cid = 8 + $i;
    $comma = ($i < 19 || 15 > 0) ? ',' : ';';
    echo "(2, $cid)$comma\n";
}
for ($i = 0; $i < 15; $i++) {
    $cid = 28 + $i;
    $comma = ($i < 14) ? ',' : ';';
    echo "(3, $cid)$comma\n";
}
echo "\n";

// Accounts: each client gets a checking (< 2000) and a savings (5000-50000)
echo "INSERT INTO accounts (user_id, account_number, balance, account_type, created_at) VALUES\n";
$accNum = 10005;
$acctStmts = [];
for ($i = 0; $i < 20; $i++) {
    $uid = 8 + $i;
    $checkBal = mt_rand(100, 1999) + mt_rand(0, 99) / 100;
    $saveBal = mt_rand(5000, 50000) + mt_rand(0, 99) / 100;
    $date = randomDate($m1Start, $m1End);
    
    $acctStmts[] = "($uid, 'ACC-$accNum', $checkBal, 'checking', '$date')";
    $accNum++;
    $acctStmts[] = "($uid, 'ACC-$accNum', $saveBal, 'savings', '$date')";
    $accNum++;
}
for ($i = 0; $i < 15; $i++) {
    $uid = 28 + $i;
    $checkBal = mt_rand(100, 1999) + mt_rand(0, 99) / 100;
    $saveBal = mt_rand(5000, 50000) + mt_rand(0, 99) / 100;
    $date = randomDate($m2Start, $m2End);
    
    $acctStmts[] = "($uid, 'ACC-$accNum', $checkBal, 'checking', '$date')";
    $accNum++;
    $acctStmts[] = "($uid, 'ACC-$accNum', $saveBal, 'savings', '$date')";
    $accNum++;
}
foreach ($acctStmts as $j => $stmt) {
    $comma = ($j < count($acctStmts) - 1) ? ',' : ';';
    echo "$stmt$comma\n";
}
echo "\n";

// Account ID mapping:
// m1 checking: accounts 5,7,9,... (odd, starting at 5)
// m1 savings: accounts 6,8,10,... (even, starting at 6)
// m2 checking: accounts 45,47,49,... 
// m2 savings: accounts 46,48,50,...

// Generate transactions between clients of the same manager
// Each pair does 2 transfers: checking -> checking, savings -> checking
echo "INSERT INTO transactions (account_id, target_account_id, type, direction, amount, status, description, created_at) VALUES\n";

$txStmts = [];
$txCount = 0;

// For manager1 (20 clients), pairs: (0,1), (2,3), ..., (18,19)
for ($p = 0; $p < 20; $p += 2) {
    $uid1 = 8 + $p;   // client A
    $uid2 = 8 + $p + 1; // client B
    
    // A's accounts: checking=$uid1*2-11, savings=$uid1*2-10
    // B's accounts: checking=$uid2*2-11, savings=$uid2*2-10
    $aChk = $uid1 * 2 - 11;
    $aSav = $uid1 * 2 - 10;
    $bChk = $uid2 * 2 - 11;
    $bSav = $uid2 * 2 - 10;
    
    $names = [$clientNames1[$p], $clientNames1[$p + 1]];
    
    // Date range for these transactions
    $txStart = clone $m1Start;
    $txEnd = clone $m1End;
    
    // 10 transactions per pair (5 each direction)
    for ($t = 0; $t < 10; $t++) {
        $amt = mt_rand(10, 500) + mt_rand(0, 99) / 100;
        $date = randomDate($txStart, $txEnd);
        $desc = "Payment to {$names[1]}";
        $txStmts[] = "($aChk, $bChk, 'transfer', 'out', -$amt, 'accepted', '$desc', '$date')";
        $txStmts[] = "($bChk, $aChk, 'transfer', 'in', $amt, 'accepted', 'Payment from {$names[0]}', '$date')";
        
        $amt2 = mt_rand(50, 1000) + mt_rand(0, 99) / 100;
        $date2 = randomDate($txStart, $txEnd);
        $desc2 = "Payment to {$names[0]}";
        $txStmts[] = "($bChk, $aChk, 'transfer', 'out', -$amt2, 'accepted', '$desc2', '$date2')";
        $txStmts[] = "($aChk, $bChk, 'transfer', 'in', $amt2, 'accepted', 'Payment from {$names[1]}', '$date2')";
    }
    
    // Also some savings -> checking transfers for each user
    for ($t = 0; $t < 5; $t++) {
        $amt3 = mt_rand(100, 2000) + mt_rand(0, 99) / 100;
        $date3 = randomDate($txStart, $txEnd);
        $txStmts[] = "($aSav, $aChk, 'transfer', 'out', -$amt3, 'accepted', 'Transfer to checking', '$date3')";
        $txStmts[] = "($aChk, $aSav, 'transfer', 'in', $amt3, 'accepted', 'Transfer from savings', '$date3')";
        
        $amt4 = mt_rand(100, 2000) + mt_rand(0, 99) / 100;
        $date4 = randomDate($txStart, $txEnd);
        $txStmts[] = "($bSav, $bChk, 'transfer', 'out', -$amt4, 'accepted', 'Transfer to checking', '$date4')";
        $txStmts[] = "($bChk, $bSav, 'transfer', 'in', $amt4, 'accepted', 'Transfer from savings', '$date4')";
    }
}

// For manager2 (15 clients), pairs: (0,1), (2,3), ..., (12,13), and 14 unpaired
for ($p = 0; $p < 14; $p += 2) {
    $uid1 = 28 + $p;
    $uid2 = 28 + $p + 1;
    
    $aChk = $uid1 * 2 - 11;
    $aSav = $uid1 * 2 - 10;
    $bChk = $uid2 * 2 - 11;
    $bSav = $uid2 * 2 - 10;
    
    $names = [$clientNames2[$p], $clientNames2[$p + 1]];
    $txStart = clone $m2Start;
    $txEnd = clone $m2End;
    
    for ($t = 0; $t < 10; $t++) {
        $amt = mt_rand(10, 500) + mt_rand(0, 99) / 100;
        $date = randomDate($txStart, $txEnd);
        $desc = "Payment to {$names[1]}";
        $txStmts[] = "($aChk, $bChk, 'transfer', 'out', -$amt, 'accepted', '$desc', '$date')";
        $txStmts[] = "($bChk, $aChk, 'transfer', 'in', $amt, 'accepted', 'Payment from {$names[0]}', '$date')";
        
        $amt2 = mt_rand(10, 500) + mt_rand(0, 99) / 100;
        $date2 = randomDate($txStart, $txEnd);
        $desc2 = "Payment to {$names[0]}";
        $txStmts[] = "($bChk, $aChk, 'transfer', 'out', -$amt2, 'accepted', '$desc2', '$date2')";
        $txStmts[] = "($aChk, $bChk, 'transfer', 'in', $amt2, 'accepted', 'Payment from {$names[1]}', '$date2')";
    }
    
    for ($t = 0; $t < 5; $t++) {
        $amt3 = mt_rand(100, 2000) + mt_rand(0, 99) / 100;
        $date3 = randomDate($txStart, $txEnd);
        $txStmts[] = "($aSav, $aChk, 'transfer', 'out', -$amt3, 'accepted', 'Transfer to checking', '$date3')";
        $txStmts[] = "($aChk, $aSav, 'transfer', 'in', $amt3, 'accepted', 'Transfer from savings', '$date3')";
        
        $amt4 = mt_rand(100, 2000) + mt_rand(0, 99) / 100;
        $date4 = randomDate($txStart, $txEnd);
        $txStmts[] = "($bSav, $bChk, 'transfer', 'out', -$amt4, 'accepted', 'Transfer to checking', '$date4')";
        $txStmts[] = "($bChk, $bSav, 'transfer', 'in', $amt4, 'accepted', 'Transfer from savings', '$date4')";
    }
}

// Pair client 14 (last m2 client, id=42) with client 13 (id=41) for extra tx
$uid1 = 41; $uid2 = 42;
$aChk = $uid1 * 2 - 11; $bChk = $uid2 * 2 - 11;
$aSav = $uid1 * 2 - 10; $bSav = $uid2 * 2 - 10;
$names = [$clientNames2[13], $clientNames2[14]];
$txStart = clone $m2Start;
$txEnd = clone $m2End;
for ($t = 0; $t < 10; $t++) {
    $amt = mt_rand(10, 500) + mt_rand(0, 99) / 100;
    $date = randomDate($txStart, $txEnd);
    $txStmts[] = "($aChk, $bChk, 'transfer', 'out', -$amt, 'accepted', 'Payment to {$names[1]}', '$date')";
    $txStmts[] = "($bChk, $aChk, 'transfer', 'in', $amt, 'accepted', 'Payment from {$names[0]}', '$date')";
    
    $amt2 = mt_rand(10, 500) + mt_rand(0, 99) / 100;
    $date2 = randomDate($txStart, $txEnd);
    $txStmts[] = "($bChk, $aChk, 'transfer', 'out', -$amt2, 'accepted', 'Payment to {$names[0]}', '$date2')";
    $txStmts[] = "($aChk, $bChk, 'transfer', 'in', $amt2, 'accepted', 'Payment from {$names[1]}', '$date2')";
}

foreach ($txStmts as $j => $stmt) {
    $comma = ($j < count($txStmts) - 1) ? ',' : ';';
    echo "$stmt$comma\n";
}

echo "\n-- Total transactions generated: " . count($txStmts) . "\n";
