<!-- Optional JavaScript; choose one of the two! -->

<!-- Option 1: Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
    crossorigin="anonymous"></script>

<!-- asset plugin datatables -->
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.2.1/js/dataTables.bootstrap5.js"></script>

<script src="https://cdn.ckeditor.com/4.25.0-lts/standard/ckeditor.js"></script>
<script>
    CKEDITIOR.replace('alamat');
</script>

<!-- load fontawesome cdn -->
<script defer src="https://use.fontawesome.com/releases/v5.15.4/js/all.js"
    integrity="sha384-rOA1PnstxnOBLzCLMcre8ybwbTmemjzdNlILg8O7z1lUkLXozs4DHonlDtnE7fpc"
    crossorigin="anonymous"></script>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<script>
    CKEDITOR.replace('alamat', {
        filebrowserBroseUrl: 'assets/ckfinder/ckfinder.html',
        filebrowserUploadUrl: 'assets/ckfinder/core/connecter/php/connector.php?command=QuickUpload&type=Files',
        height: '400px'
    });

</script>


<script>
    $(document).ready(function () {
        $('#example').DataTable();
    })

</script>

</body>

</html>