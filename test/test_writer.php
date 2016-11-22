<?php

$tests = [
    'test01_with_headers.php'                       => '19a70cfb3006022f2aa4b9192e9db41e',
    'test02_without_headers_assoc.php'              => '69f74f94a7944fb6292679a17edcf09e',
    'test03_without_headers_ordered.php'            => '69f74f94a7944fb6292679a17edcf09e',
];

echo "Testing CsvWriter:\n";

$passed = 0;
$failed = 0;
foreach ($tests as $testScript => $expectedOutputMd5) {
    if (($md5sum = getMd5OfCommandOutput('php tests/writer/' . $testScript)) === $expectedOutputMd5) {
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
