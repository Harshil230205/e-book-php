<?php
include 'config/database.php';
include 'config/session.php';

// Get book ID from URL
$book_id = $_GET['id'] ?? 0;
$action = $_GET['action'] ?? 'view'; // 'view' or 'download'

if (!$book_id) {
    http_response_code(404);
    exit('Book not found');
}

// Get book details
$book_query = "SELECT title, pdf_file FROM books WHERE id = ? AND status = 'approved'";
$stmt = mysqli_prepare($conn, $book_query);
mysqli_stmt_bind_param($stmt, "i", $book_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    http_response_code(404);
    exit('Book not found');
}

$book = mysqli_fetch_assoc($result);

if (empty($book['pdf_file'])) {
    http_response_code(404);
    exit('PDF file not found');
}

// Get PDF content from Cloudinary
$pdf_url = $book['pdf_file'];
$ch = curl_init($pdf_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
$data = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || $data === false) {
    http_response_code($httpCode ?: 500);
    exit('Failed to fetch PDF file');
}

// Clean filename
$filename = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $book['title']);
$filename = trim($filename) . '.pdf';

// Set appropriate headers
header('Content-Type: application/pdf');
header('X-Content-Type-Options: nosniff');
header('Content-Length: ' . strlen($data));

if ($action === 'download') {
    // Force download
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: public, must-revalidate, max-age=0');
    header('Pragma: public');
} else {
    // Display in browser
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Cache-Control: public, max-age=3600');
}

// Output PDF content
echo $data;
exit();
?>