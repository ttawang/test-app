<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>User</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.0/css/dataTables.bootstrap.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <table class="table" id="table-detail">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Photo</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
    <div class="modal" tabindex="-1" id="modal-kelola">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        onclick="closeModaal()"></button>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" id="form" action="" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Name</label>
                            <input type="name" class="form-control" id="name" name="name">
                        </div>
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        onclick="closeModal()">Close</button>
                    <button type="button" class="btn btn-primary" onclick="simpan($(this))">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<script src="https://cdn.datatables.net/2.1.0/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.1.0/js/dataTables.bootstrap.min.js"></script>
<script type="text/javascript">
    var table = '';
    $(function() {
        tableDetail();
    });

    function tableDetail() {
        table = $('#table-detail').DataTable({
            order: [],
            ajax: `{{ url('user/table') }}`,
            lengthMenu: [15, 25, 50, 100],
            processing: true,
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'photo',
                    name: 'photo'
                },
                {
                    data: 'action',
                    name: 'action'
                }
            ]
        });
    }

    function simpan(elem) {
        var form = $('#form')[0];
        var formData = new FormData(form);
        $.ajax({
            url: `{{ url('user/simpan') }}`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: formData,
            processData: false,
            contentType: false,
            success: function(respon) {
                if (respon.success == true) {
                    closeModal();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: respon.message,
                    }).then((result) => {
                        tableDetail.ajax.reload();
                    });
                } else {
                    $('#modal-kelola').modal('hide');
                    let errorMessage = '';
                    $.each(respon.messages, function(fieldName, fieldErrors) {
                        errorMessage += fieldErrors[0];
                        if (fieldName !== Object.keys(respon.messages).slice(-1)[0]) {
                            errorMessage += ', ';
                        }
                    });

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: errorMessage,
                    }).then((result) => {
                        $('#modal-kelola').modal('show');
                        tableDetail.ajax.reload();
                    });
                }
            },
            error: function(xhr, status, error) {
                $('#modal-kelola').modal('hide');
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: xhr.responseJSON.messages,
                }).then((result) => {
                    $('#modal-kelola').modal('show');
                    tableDetail.ajax.reload();
                });
            }
        });
    }

    function tambah(elem) {
        $('.modal-title').text('Tambah');
        $('#modal-kelola').modal('show');
    }

    function closeModal() {
        $('#modal-kelola').modal('hide');
        $('#id').val('');
        $('#name').val('');
        $('#email').val('');

    }

    function hapus(elem) {
        Swal.fire({
            title: "Apakah anda yakin ?",
            text: "Data yang dihapus tidak dapat dikembalikan",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Hapus",
            cancelButtonText: "Close",
            reverseButtons: true,
        }).then(function(result) {
            if (result.value === true) {
                var id = elem.data('id');
                $.ajax({
                    url: `{{ url('user/hapus') }}`,
                    type: 'get',
                    data: {
                        id: id
                    },
                    success: function(respon) {
                        if (respon.success == true) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: respon.message,
                            }).then((result) => {
                                tableDetail.ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Data gagal dihapus',
                            }).then((result) => {
                                tableDetail.ajax.reload();
                            });
                        }
                    }
                });
            }
        });
    }

    function edit(elem) {
        $('.modal-title').text('Edit');
        $('#modal-kelola').modal('show');
        var id = elem.data('id');
        $.get(`{{ url('user/get-data') }}/${id}`, function(data) {
            $('#id').val(data.id);
            $('#name').val(data.name);
            $('#email').val(data.email);
        });
    }
</script>

</html>
