'use strict';

var mtask = new function(){
    this.init = function()
    {
        mtask.widgets.init();
        mtask.blocks.init();
    }
}


/*
* The portal sections
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


/*
* The section of downloading info from portal functional
* */
mtask.blocks.loader = function()
{
    var loader = this;

    var $notification;
    // An array of downloaded users
    loader.arUsers = [];
    // An array of downloaded time
    loader.timesLoadedCnt = 0;
    // Inserted to DB Time intervals
    loader.timesInsertedCnt = 0;
    // Updated in DB Time intervals
    loader.timesUpdatedCnt = 0;
    // Delay between actions
    loader.delay = 0;
    // The current task number
    loader.task = 0;
    // The portal Tasks count
    loader.tasksCnt = 0;
    // Downloading task
    loader.loadingTask = 0;
    // Downloaded tasks count
    loader.tasksLoadedCnt = 0;
    // Inserted to DB Tasks
    loader.tasksInsertedCnt = 0;
    // Updated in DB Tasks
    loader.tasksUpdatedCnt = 0;
    // An array of the Tasks which have a duration
    loader.arDurationTasks = {};
    // Current page number of downloading tasks
    loader.tasksPage = 1;

    /**
     * The function which call one of the downloading subfunction
     */
    this.updateInfo = function(action, arParams) {
        if( action == undefined ){
            action = 'getGroups';
        }
        if( arParams == undefined ){
            arParams = {};
        }

        $.ajax({
            url : window.location.href,
            data: {ACTION: action, PARAMS: arParams},
            dataType: 'json',
            method: 'post',
            success: function(response) {
                switch(action) {

                    /* Callback for getting groups */
                    case 'getGroups':
                        action = 'getTasks';
                        loader.addNotification(response, 'групп', 'задач');
                        loader.updateInfo(action, {});
                        break;

                    /* Callback for getting tasks */
                    case 'getTasks':
                        if( response.hasOwnProperty('TOTAL')
                            && response.hasOwnProperty('ITEMS')
                            && Object.keys(response.ITEMS).length
                            && (loader.tasksLoadedCnt + Object.keys(response.ITEMS).length) < response.TOTAL ){
                            loader.addNotification(response, 'задач');

                            if( response.hasOwnProperty('TASKS_WITH_DURATIONS') && Object.keys(response.TASKS_WITH_DURATIONS).length ){
                                loader.tasksCnt = Object.keys(response['TASKS_WITH_DURATIONS']).length;
                                $.extend(loader.arDurationTasks, loader.arDurationTasks, response.TASKS_WITH_DURATIONS)
                            }

                            loader.tasksPage++;
                            loader.updateInfo(action, {'PAGE': loader.tasksPage});
                        }
                        else{
                            loader.addNotification(response, 'задач', 'списаний времени');
                            action = 'getTime';

                            $.each(loader.arDurationTasks, function (index, arTask) {
                                setTimeout(function(){
                                    loader.task++;
                                    loader.loadingTask = index;
                                    loader.updateInfo(action, {'TASK': arTask})
                                }, loader.delay += 100);
                            });
                        }

                        break;

                    /* Callback for getting time intervals */
                    case 'getTime':
                        loader.addNotification(response, 'списаний времени')
                        if( response.hasOwnProperty('USERS') && Object.keys(response.USERS).length ){
                            $.each(response.USERS, function(index, val){
                                if( $.inArray(index, loader.arUsers) == -1 ){
                                    loader.arUsers.push(index);
                                }
                            });
                        }
                        if( loader.task == loader.tasksCnt && response['TASK'] == loader.loadingTask ){
                            action = 'getUsers';
                            loader.addNotification(response, '', 'пользователей');
                            loader.updateInfo(action, {'USERS': loader.arUsers});
                        }
                        break;

                    /* Callback for getting users */
                    case 'getUsers':
                        loader.addNotification(response, 'пользователей');
                        break;
                }
            }
        });
    };

    /**
     * The functional of adding notification about any action of downloading from portal
     */
    this.addNotification = function(arLoaded, currentEntity, nextEntity)
    {
        var $contentWrap = $('.js-update-portal-info');
        var $addContent = $('<pre>');
        var content,
            loadedCnt,
            insertedCnt,
            updatedCnt;

        if( currentEntity != undefined && currentEntity != '' ){
            loadedCnt = (( arLoaded.hasOwnProperty('ITEMS') && Object.keys(arLoaded.ITEMS).length ) ? Object.keys(arLoaded.ITEMS).length : 0);
            insertedCnt = (( arLoaded.hasOwnProperty('INSERTED') && Object.keys(arLoaded.INSERTED).length ) ? Object.keys(arLoaded.INSERTED).length : 0);
            updatedCnt = (( arLoaded.hasOwnProperty('UPDATED') && Object.keys(arLoaded.UPDATED).length ) ? Object.keys(arLoaded.UPDATED).length : 0);

            if( currentEntity == 'списаний времени' ){
                loader.timesLoadedCnt = loadedCnt = loader.timesLoadedCnt + loadedCnt;
                loader.timesInsertedCnt = insertedCnt = loader.timesInsertedCnt + insertedCnt;
                loader.timesUpdatedCnt = updatedCnt = loader.timesUpdatedCnt + updatedCnt;
            }

            content = 'Загружено ' + currentEntity + ' с портала: ' + loadedCnt;

            if( currentEntity == 'задач' ){
                loader.tasksLoadedCnt = loadedCnt = loader.tasksLoadedCnt + loadedCnt;
                loader.tasksInsertedCnt = insertedCnt = loader.tasksInsertedCnt + insertedCnt;
                loader.tasksUpdatedCnt = updatedCnt = loader.tasksUpdatedCnt + updatedCnt;
            }
            content += '<br>Добавлено записей в БД: ' + insertedCnt;
            content += '<br>Обновлено записей в БД: ' + updatedCnt;

            $addContent.clone().html(content).appendTo($contentWrap);
        }

        if( nextEntity != undefined && nextEntity != '' ){
            $addContent.clone().html('Идет загрузка ' + nextEntity).appendTo($contentWrap);
        }
    }


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
     * The tablesorter widget
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
            );

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
        "pagingType": "numbers",
    };


    /**
     * Widgets initialization
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