"use strict";


const { default: Swal } = require("sweetalert2");
$(function () {
    $("#logout-btn").on("click", function () {
        Swal.fire({
            title: "Keluar ?",
            text: "Keluar dari sistem e-KPB!",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Keluar!",
            cancelButtonText: "Batalkan",
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById("logout-form").submit();
            }
        });
    });

    $('#customFile').on('change', function(e) {
        $(this).siblings().text(e.target.value.split('fakepath')[1].replace('\/\/', ''))
    })
});
