<?php

$tests = [
    'test01_foreach_open.php'                       => 'c8dbb23a87e985315bdc5bce1ee55832',
    'test02_foreach_assign.php'                     => 'c8dbb23a87e985315bdc5bce1ee55832',
    'test03_while_open.php'                         => 'c8dbb23a87e985315bdc5bce1ee55832',
    'test04_while_assign.php'                       => 'c8dbb23a87e985315bdc5bce1ee55832',
    'test05_without_headers.php'                    => '3a21c1669ef408a65b7a7290331e36bd',
    'test06_force_headers.php'                      => 'fea60409e4b38b8f4294d104c90e7413',
    'test07_override_headers_hardcoded.php'         => 'f305f0105da1c660f205d02e5d2d657c',
    'test08_override_headers_reflection.php'        => 'f305f0105da1c660f205d02e5d2d657c',
    'test09_reuse_reader_open_open_foreach.php'     => '38347921852a9033cfa1a6391890ec9b',
    'test10_reuse_reader_open_assign_foreach.php'   => '38347921852a9033cfa1a6391890ec9b',
    'test11_reuse_reader_assign_open_foreach.php'   => '38347921852a9033cfa1a6391890ec9b',
    'test12_reuse_reader_assign_assign_foreach.php' => '38347921852a9033cfa1a6391890ec9b',
    'test13_reuse_reader_open_open_while.php'       => '38347921852a9033cfa1a6391890ec9b',
    'test14_reuse_reader_open_assign_while.php'     => '38347921852a9033cfa1a6391890ec9b',
    'test15_reuse_reader_assign_open_while.php'     => '38347921852a9033cfa1a6391890ec9b',
    'test16_reuse_reader_assign_assign_while.php'   => '38347921852a9033cfa1a6391890ec9b',
    'test17_read_broken_with_headers.php'           => '88348b8d43c1e7bf46a9d1be748dcef5',
    'test18_read_broken_without_headers.php'        => '5cd5d0533aae7f50b01727bdeba4eeac',
    'test19_multiline_with_header.php'              => '6983a8764438d8ab6fca6d90790146e9',
    'test20_multiline_without_header.php'           => 'a57678af713c2f425093100b2c89a6e5',
];

echo "Testing CsvReader:\n";

$passed = 0;
$failed = 0;
foreach ($tests as $testScript => $expectedOutputMd5) {
    if (($md5sum = getMd5OfCommandOutput('php tests/reader/' . $testScript)) === $expectedOutputMd5) {
        echo '.';
        $passed++;
    } else {
        echo 'E';
        //echo "\n" . $md5sum . "\n";
        $failed++;
    }
}

echo "\n\nTotal tests: ", $passed + $failed, "\n";
echo "Passed: {$passed}\n";
echo "Failed: {$failed}\n";

function getMd5OfCommandOutput($cmd)
{
    ob_start();
    passthru($cmd, $exitCode);
    if ($exitCode !== 0) {
        fwrite(STDERR, "Failed execute command: \"{$cmd}\"\n");
    }
    $output = ob_get_contents();
    ob_end_clean();
    return md5($output);
}
