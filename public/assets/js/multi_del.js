$(function () {
    $("#select_all_ids").click(function () {
        $(".checkbox_ids").prop("checked", $(this).prop("checked"));
    });
});

$("#confirmDelete").click(function () {
    // Mendapatkan URL dari atribut data-url
    var url = $("#deleteAllSelectedRecord").data("url");

    // Mendapatkan token CSRF dari meta tag
    var csrfToken = $('meta[name="csrf-token"]').attr("content");

    // Deklarasikan variabel all_ids dan berikan nilai yang sesuai
    var all_ids = []; // Misalnya, Anda dapat menginisialisasi dengan array kosong

    // Mengumpulkan ID yang ingin dihapus, misalnya dari input checkbox
    $("input:checkbox[name=ids]:checked").each(function () {
        all_ids.push($(this).val());
    });

    // Lakukan permintaan Ajax dengan metode POST dan spoofing DELETE
    $.ajax({
        url: url,
        type: "POST", // Mengubah metode menjadi POST
        data: {
            _method: "DELETE", // Menambahkan metode spoofing DELETE
            ids: all_ids,
            _token: csrfToken,
        },
        success: function (response) {
            $.each(all_ids, function (key, val) {
                $("#upt_ids" + val).remove();
                location.reload();
            });
        },
        error: function (xhr, textStatus, errorThrown) {
            console.error(xhr.responseText); // Log pesan kesalahan ke konsol
            // Handle error
        },
    });
    // Tutup modal peringatan
    $("#confirmationModal").modal("hide");
});
