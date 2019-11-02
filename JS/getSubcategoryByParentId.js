$(function () {    
    init_cut();    
});

function init_cut(){    
    $('#categoryId').change(function () {
        if (this.value != '0') {
            $.ajax({
                url: '/ajax/getSubcategoryByParentId.php',
                dataType: 'text',
                data: 'parentid=' + this.value,
                converters: 'json text',
                method: 'POST'
            })
                    .done(function (obj) {
                        obj = $.parseJSON(obj);
                        subcategories = obj.results;
                        sel = $("#subcategoryId");
                        sel.empty();                        
                        subcategories.forEach(function(item, index, arr){
                            sel.append($('<option value="'+item.id+'">'+item.description+'</option>'));
                        });
                    })
                    .fail(function (xhr, status, error) {
                        console.log('Ошибка соединения с сервером (POST)');
                        console.log('ajaxError xhr:', xhr); // выводим значения переменных
                        console.log('ajaxError status:', status);
                        console.log('ajaxError error:', error);
                    });
            return false;
        }
    });    
}