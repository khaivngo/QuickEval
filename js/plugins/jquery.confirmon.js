/*!
 * jQuery confirmOn Plugin 0.1.0
 * https://github.com/invetek/jquery-confirmon
 *
 * Copyright 2013 Loran Kloeze - Invetek
 * Released under the MIT license
 */

/*
 * Bug(?): event.preventDefault() has to be called to prevent native handlers  (line 176) 
 * 
 */

(function($) {
    var confirmOn = $.confirmOn = {};

    confirmOn.providedOptions = {};

    confirmOn.defaultSettings = {
        questionText: 'Are you sure?',
        classPrepend: 'confirmon',
        textYes: 'Yes',
        textNo: 'No'
    };

    confirmOn.overrideDefaultSettings = function(settings) {
        confirmOn.defaultSettings = $.extend({}, confirmOn.defaultSettings, settings);
    };

    confirmOn.setOptions = function($element, options) {
        options = $.extend({}, confirmOn.defaultSettings, options);
        $element.data('confirmon', {
            options: options
        });
    };

    confirmOn.createOverlay = function($element) {
        var classPrepend = $element.data('confirmon').options.classPrepend;
        return $('<div/>').addClass(classPrepend + '-overlay').hide().appendTo('body');
    };

    confirmOn.showOverlay = function($element) {
        var classPrepend = $element.data('confirmon').options.classPrepend;
        $('.' + classPrepend + '-overlay').fadeIn();
    };

    confirmOn.deleteOverlay = function($element) {
        var classPrepend = $element.data('confirmon').options.classPrepend;
        $('.' + classPrepend + '-overlay').fadeOut(function(){
            $(this).remove();
        });
    };

    confirmOn.createBox = function($element) {
        var classPrepend = $element.data('confirmon').options.classPrepend;
        var questionText = $element.data('confirmon').options.questionText;
        var textYes = $element.data('confirmon').options.textYes;
        var textNo = $element.data('confirmon').options.textNo;

        var $box = $('<div/>').addClass(classPrepend + '-box').hide().appendTo('body');
        $('<p/>').html(questionText).appendTo($box);
        $('<button/>').html(textYes).appendTo($box);
        $('<button/>').html(textNo).appendTo($box);
    };

    confirmOn.showBox = function($element) {
        var classPrepend = $element.data('confirmon').options.classPrepend;
        $('.' + classPrepend + '-box').fadeIn();
    };

    confirmOn.deleteBox = function($element) {
        var classPrepend = $element.data('confirmon').options.classPrepend;
        $('.' + classPrepend + '-box').fadeOut(function(){
            $(this).remove();
        });
    };

    confirmOn.convertArguments = function(options, events, selector, data, handler) {
        if (typeof options === 'object') {
            $.each(options, function(key, val) {
                if (typeof val === 'string') { //Options provided so shift all args to left
                    confirmOn.providedOptions = options;

                    options = events;
                    events = selector;
                    selector = data;
                    data = handler;
                    return false;
                } else { //No options
                    confirmOn.providedOptions = {};
                }
            });
        } else {
           confirmOn.providedOptions = {}; 
        }
                
        if (selector == null && data == null && handler == null) {
            //(events[S], handler)
            selector = events;
            events = options;
        } else if (data == null && handler == null) {
            //(events[S], selector, handler)
            //(events[S], data, handler)
            data = selector;
            selector = events;
            events = options;
        } else {
            handler = data;
            data = selector;
            selector = events;
            events = options;
        }

        if (typeof events === 'object') {
            //Implementation .on( events [, selector ] [, data ] )
            return {
                events: events,
                selector: selector,
                data: data
            };
        } else {
            //Implementation .on( events [, selector ] [, data ], handler(eventObject) )
            return {
                events: events,
                selector: selector,
                data: data,
                handler: handler
            };
        }


    };
    
    $.confirmOn.attachYesHandler = function($element, handler, event) {
        var classPrepend = $element.data('confirmon').options.classPrepend;
        $('.' + classPrepend + '-box button').eq(0).on('click', function(){
            $.confirmOn.deleteOverlay($element);
            $.confirmOn.deleteBox($element);
            handler.call($element.get(), event);
            
        });
        
    };
    
    $.confirmOn.attachNoHandler = function($element) {
        var classPrepend = $element.data('confirmon').options.classPrepend;
        $('.' + classPrepend + '-box button').eq(1).on('click', function(){
            $.confirmOn.deleteOverlay($element);
            $.confirmOn.deleteBox($element);
        });
    };

    $.fn.confirmOn = function(options, events, selector, data, handler) {
        var userHandler;
        if (typeof events === 'function') {
            userHandler = events;
            events = confirmHandler;
        } else if (typeof selector === 'function') {
            userHandler = selector;
            selector = confirmHandler;
        } else if (typeof data === 'function') {
            userHandler = data;
            data = confirmHandler;
        } else if (typeof handler === 'function') {
            userHandler = handler;
            handler = confirmHandler;
        }

        var $element = $(this);
        var onArgs = $.confirmOn.convertArguments(options, events, selector, data, handler);
        $.confirmOn.setOptions($element, $.confirmOn.providedOptions);

        $element.on(onArgs.events, onArgs.selector, onArgs.data, onArgs.handler);

        function confirmHandler(event) {
            event.preventDefault();
            $.confirmOn.createOverlay($element);
            $.confirmOn.showOverlay($element);
            $.confirmOn.createBox($element);
            $.confirmOn.showBox($element);
            $.confirmOn.attachYesHandler($element, userHandler, event);
            $.confirmOn.attachNoHandler($element);
            
        }
        ;

    };

}(jQuery));