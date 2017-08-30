<?php

$tests = [
    'php tests/reader/test01_foreach_open.php'                       => 'c8dbb23a87e985315bdc5bce1ee55832',
    'php tests/reader/test02_foreach_assign.php'                     => 'c8dbb23a87e985315bdc5bce1ee55832',
    'php tests/reader/test03_while_open.php'                         => 'c8dbb23a87e985315bdc5bce1ee55832',
    'php tests/reader/test04_while_assign.php'                       => 'c8dbb23a87e985315bdc5bce1ee55832',
    'php tests/reader/test05_without_headers.php'                    => '3a21c1669ef408a65b7a7290331e36bd',
    'php tests/reader/test06_force_headers.php'                      => 'fea60409e4b38b8f4294d104c90e7413',
    'php tests/reader/test07_override_headers_hardcoded.php'         => 'f305f0105da1c660f205d02e5d2d657c',
    'php tests/reader/test08_override_headers_reflection.php'        => 'f305f0105da1c660f205d02e5d2d657c',
    'php tests/reader/test09_reuse_reader_open_open_foreach.php'     => '38347921852a9033cfa1a6391890ec9b',
    'php tests/reader/test10_reuse_reader_open_assign_foreach.php'   => '38347921852a9033cfa1a6391890ec9b',
    'php tests/reader/test11_reuse_reader_assign_open_foreach.php'   => '38347921852a9033cfa1a6391890ec9b',
    'php tests/reader/test12_reuse_reader_assign_assign_foreach.php' => '38347921852a9033cfa1a6391890ec9b',
    'php tests/reader/test13_reuse_reader_open_open_while.php'       => '38347921852a9033cfa1a6391890ec9b',
    'php tests/reader/test14_reuse_reader_open_assign_while.php'     => '38347921852a9033cfa1a6391890ec9b',
    'php tests/reader/test15_reuse_reader_assign_open_while.php'     => '38347921852a9033cfa1a6391890ec9b',
    'php tests/reader/test16_reuse_reader_assign_assign_while.php'   => '38347921852a9033cfa1a6391890ec9b',
    'php tests/reader/test17_read_broken_with_headers.php'           => '61553e77c49cf41b4db12dd1042539b8',
    'php tests/reader/test18_read_broken_without_headers.php'        => 'd522ccda344d2df42e6cdfdb11d6b6ef',
    'php tests/reader/test19_multiline_with_header.php'              => [
        'f4f560f35a86eefb83b977e3f2347a5a', // unix line-endings
        '6983a8764438d8ab6fca6d90790146e9', // windows line-endings
    ],
    'php tests/reader/test20_multiline_without_header.php'           => [
        '53329052325a3280f324baf731a31b72', // unix line-endings
        'a57678af713c2f425093100b2c89a6e5', // windows line-endings
    ],
    'php -r "echo file_get_contents(\'samples/sample-10.with-header.csv\');" | php tests/reader/test21_read_from_nonseekable_stdin.php' => 'c8dbb23a87e985315bdc5bce1ee55832',
    'php -r "echo file_get_contents(\'samples/sample-10.with-header.csv\');" | php tests/reader/test22_rewind_in_nonseekable_stdin.php' => '32fb245669231162f3679a91bd87bfdd',
    'php tests/reader/test23_ignore_empty_lines.php'                 => '87666d6c838a0317186a6a246bbd228e',
    'php tests/reader/test24_not_ignore_empty_lines.php'             => 'c8515a41d6ef604ee703d1056c0dd908',
];

echo "Testing CsvReader:\n";

$passed = 0;
$failed = 0;
foreach ($tests as $testScript => $expectedOutputMd5) {
    if (in_array($md5sum = getMd5OfCommandOutput($testScript), is_array($expectedOutputMd5) ? $expectedOutputMd5 : [$expectedOutputMd5])) {
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
