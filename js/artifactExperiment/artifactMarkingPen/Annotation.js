(function() {

    /**
     * Define our constructor.
     * The 'this' keyword is pointing to the window object, which means we're
     * attaching our constructor to the global scope.
     */
    this.AnnotationModal = function() {
        'use strict';

        /* Create global element references */
        this.modal = null;
        this.annotationForm = null;
        this.annotationInput = null;
        this.overlay = null;

        /* Define option defaults */
        var defaults = {
            className: 'fade-and-drop',
            content: '',
            inputValue: '',
            placeholderText: '',
            maxWidth: 400,
            minWidth: 280
        }

        /**
         * The arguments object. This is a magical object inside of every function that contains
         * an array of everything passed to it via arguments. Because we are only expecting
         * one argument, an object containing plugin settings, we check to make sure arguments[0]
         * exists, and that it is indeed an object.
         * -------------------
         * create options by extending defaults with the passed in arguments
         */
        if (arguments[0] && typeof arguments[0] === "object") {
            /* merge the two objects using a privately scoped utility method called extendDefaults */
            this.options = extendDefaults(defaults, arguments[0]);
        }
    }


    /**
     * Open the modal.
     */
    AnnotationModal.prototype.open = function() {
        /*
         * Build out our Modal.
         * Call our createMarkup method using the call method, similarly to the way we
         * did in our event binding with bind. We are simply passing the proper value of this to the method.
         */
        createMarkup.call(this);

        /* Initialize our event listeners */
        initializeEvents.call(this);

        /*
         * After adding elements to the DOM, use getComputedStyle
         * to force the browser to recalc and recognize the elements
         * that we just added. This is so that CSS animation has a start point
         */
        window.getComputedStyle(this.modal).height;

        /*
         * Add our open class and check if the modal is taller than the window.
         * If so, our anchored class is also applied.
         */
        this.modal.className =
            this.modal.className + (this.modal.offsetHeight > window.innerHeight ?
                " annotation-modal-open annotation-modal-anchored" : " annotation-modal-open");

        this.overlay.className = this.overlay.className + " annotation-modal-open";
    }

    /**
     * Close the modal by removing the nodes from the DOM when the CSS animation has ended.
     */
    AnnotationModal.prototype.close = function(event) {
        /* stop the form from submitting */
        event.preventDefault();

        /* Store the current value of this */
        var self = this;

        removeEvents.call(this);

        /* Remove the open class name */
        this.modal.className = this.modal.className.replace(" annotation-modal-open", "");
        this.overlay.className = this.overlay.className.replace(" annotation-modal-open", "");

        /*
         * Listen for CSS transitionend event and then
         * Remove the nodes from the DOM
         */
        this.modal.addEventListener('transitionend', function() {
            self.modal.parentNode.removeChild(self.modal);
        });

        this.overlay.addEventListener('transitionend', function() {
            if (self.overlay.parentNode) {
                self.overlay.parentNode.removeChild(self.overlay);
            }
        });

        /* trigger the  */
        if (this.options.hasOwnProperty('onClose')) {
            this.options.onClose();
        }
    }

    /**
     * Public method for getting the value of the input text field.
     */
    AnnotationModal.prototype.getInputValue = function() {
        return this.annotationInput.value;
    }

    /**
     * Build the HTML for the modal.
     */
    function createMarkup() {

        /* Create a DocumentFragment to build with */
        var docFrag = document.createDocumentFragment();

        /* Create modal element */
        this.modal = document.createElement("div");
        this.modal.className = "annotation-modal " + this.options.className;
        this.modal.style.minWidth = this.options.minWidth + "px";
        this.modal.style.maxWidth = this.options.maxWidth + "px";

        /* Create overlay */
        this.overlay = document.createElement("div");
        this.overlay.className = "annotation-modal-overlay " + this.options.classname;
        docFrag.appendChild(this.overlay);

        /* Create content area */
        var contentHolder = document.createElement("div");
        contentHolder.className = "annotation-modal-content";

        /* Create form */
        this.annotationForm = document.createElement("form");
        this.annotationForm.className = 'annotation-modal-form';
        this.annotationForm.style.margin = 0;
        this.annotationForm.style.padding = 0;

        /* Create form input */
        this.annotationInput = document.createElement("input");
        this.annotationInput.setAttribute("type", "text");
        this.annotationInput.setAttribute("placeholder", this.options.placeholderText);
        this.annotationInput.value = this.options.inputValue;
        this.annotationInput.className = 'annotation-modal-input';

        /* Append input to form */
        this.annotationForm.appendChild(this.annotationInput);

        /* Append form to contet area */
        contentHolder.appendChild(this.annotationForm);

        /* Append content area to modal */
        this.modal.appendChild(contentHolder);

        /* Append modal to DocumentFragment */
        docFrag.appendChild(this.modal);

        /* Append DocumentFragment to body */
        document.body.appendChild(docFrag);

        /* automatically put mouse focus inside the input field */
        this.annotationInput.focus();
    }


    function initializeEvents(event) {
        this.overlay.addEventListener(
            /*
             * Call close by using the bind method, which makes sure
             * this will reference the same thing inside our close method,
             * which is our modal object.
             */
            'click', this.close.bind(this)
        );

        this.annotationForm.addEventListener(
            'submit', this.close.bind(this)
        );
    }

    /**
     * Removes event listeners to avoid duplication when the modal
     * is opened and closed multiple times.
     */
    function removeEvents(event) {
        this.overlay.removeEventListener(
            'click', this.close.bind(this)
        );

        this.annotationForm.removeEventListener(
            'submit', this.close.bind(this)
        );
    }



    /* Extend defaults with user options. */
    function extendDefaults(source, properties) {
        var property;
        for (property in properties) {
            if (properties.hasOwnProperty(property)) {
                source[property] = properties[property];
            }
        }
        return source;
    }

    /* forEach method for iterating thorugh NodeLists.
     * Such as the one returned from querySelectorAll */
    function forEach(array, callback, scope) {
        for (var i = 0; i < array.length; i++) {
            /* passes back stuff we need */
            callback.call(scope, i, array[i]);
        }
    }

}());
