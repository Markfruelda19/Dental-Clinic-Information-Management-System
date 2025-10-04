<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select2 Test</title>
    <!-- Load jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
    <!-- Load Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
    <!-- Load Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
</head>
<body>
    <!-- Dropdown -->
    <select id="testSelect" multiple>
        <option value="1">Option 1</option>
        <option value="2">Option 2</option>
        <option value="3">Option 3</option>
    </select>

    <script>
        $(document).ready(function () {
            console.log('jQuery Version:', $.fn.jquery); // Log jQuery
            console.log('Select2 Loaded:', $.fn.select2); // Log Select2
            $('#testSelect').select2({
                placeholder: 'Select an option',
                allowClear: true
            });
        });
    </script>
</body>
</html>
