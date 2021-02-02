jQuery(function () {
    $(".btn_data").click(function () {
        let this_id = this.id;
        if(this_id) {
            $(".btn_data").prop('disabled',true)
                .removeClass('btn-danger text-white');
            $("#div_top_datas").html('<p align="center"><img src="../_publics/images/loading_white.gif" /></p>');
            $.ajax({
                url: '../_configs/Includes/Searches/Ogd/search_statistiques_top_datas.php',
                type:' POST',
                data: {
                    'type_donnees': this_id
                },
                success: function (data) {
                    $("#div_top_datas").html(data);
                    $("#"+this_id).addClass('btn-danger text-white');
                    $(".btn_data").prop('disabled',false);
                }
            });
        }
    });
    $('.dataTable').DataTable();
});