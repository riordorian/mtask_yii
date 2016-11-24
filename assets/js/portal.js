'use strict';

var mtask = new function(){
    this.init = function()
    {
        mtask.widgets.init();
        mtask.blocks.init();
    }
}


/*
* Блоки портала
* */
mtask.blocks = new function()
{
    this.init = function ()
    {
        var constructor;

        for(var key in this){
            constructor = this[key];
            var bBlockExists = constructor.exists ? constructor.exists() : true;
            if( bBlockExists ){
                this.key = new constructor();
            }
        }
    }
}

mtask.blocks.loader = function()
{
    var loader = this;

    var $contentWrap = $('.js-update-portal-info');
    var $addContent = $('<pre>');
    var $notification,
        content;
    var arMethods = [
        {'methodName': 'getGroups', 'methodTitle': 'групп'},
        {'methodName': 'getTasks', 'methodTitle': 'задач'}
    ];
    var k = 0;

    this.updateInfo = function(action, arParams) {
        if( action == undefined ){
            action = 'getGroups';
        }
        if( arParams == undefined ){
            arParams = {};
        }
        console.log(action);

        $.ajax({
            url : window.location.href,
            data: {ACTION: action, PARAMS: arParams},
            dataType: 'json',
            method: 'post',
            success: function(response) {
                content = 'Загружено ' + arMethods[k].methodTitle + ' с портала: ' + (( response.hasOwnProperty('ITEMS') && Object.keys(response.ITEMS).length ) ? Object.keys(response.ITEMS).length : 0);
                content += '<br>Добавлено записей в БД: ' + (( response.hasOwnProperty('INSERTED') && Object.keys(response.INSERTED).length ) ? Object.keys(response.INSERTED).length : 0);
                content += '<br>Обновлено записей в БД: ' + (( response.hasOwnProperty('UPDATED') && Object.keys(response.UPDATED).length ) ? Object.keys(response.UPDATED).length : 0);
                $notification = $addContent.clone().html(content).appendTo($contentWrap);
                k++;

                if( k < arMethods.length ){
                    loader.updateInfo(arMethods[k].methodName, {'ITEMS': response.ITEMS});
                    $addContent.clone().html('Идет загрузка ' + arMethods[k].methodTitle).appendTo($contentWrap);
                }

            }
        });
    };

    this.updateInfo();
}
mtask.blocks.loader.exists = function()
{
    return $('.js-update-portal-info').length;
}


/*
* Виджеты
* */
mtask.widgets = new function()
{
    this.items = {};

    /*
     * Виджет сортировки таблиц
     * */
    this.items['datatable'] = function(selector)
    {
        if (!$.fn.DataTable) {
            return;
        }

        var defaults = mtask.widgets.items.datatable.defaults;

        $(selector).each(function() {
            var element = $(this);

            var arConfig = $.extend(
                {},
                defaults,
                element.data('config') || {}
            )

            element.DataTable(arConfig);
        });
    };

    this.items['datatable'].defaults = {
        "language": {
            "lengthMenu": "_MENU_ Записей на странице",
            "zeroRecords": "По вашему запросу записи не найдены.",
            "info": "Страница _PAGE_ из _PAGES_",
            "infoEmpty": "По вашему запросу записи не найдены.",
            "infoFiltered": "(Отфильтровано из _MAX_ записей.)"
        },
        "pagingType": "numbers"
    };


    /**
     * Инициализация виджетов
     */
    this.init = function($selector)
    {
        if( $selector == undefined || !$selector.length ){
            $selector = $('body');
        }

        $.each(this.items, function(widgetName){
            this.call(this, $selector.find('.widget.' + widgetName));
        });
    }
}


$(document).ready(function() {
    mtask.init();
});