<template>
  <div
    class="canvas-container"
    style="position: relative;"
    oncontextmenu="return false;"
  >
    <canvas id="savedShapes"></canvas>
    <canvas id="currentDrawing"></canvas>
    <!-- image canvas goes here -->
  </div>
</template>

<script>
import lodashGroupBy from 'lodash/groupBy'
import lodashEach from 'lodash/each'

// keep track of unique id for each instance of this component
let uuid = 0

export default {
  beforeCreate () {
    this.uuid = uuid.toString()
    uuid += 1
  },

  props: ['imageURL', 'tool'],

  watch: {
    imageURL () {
      this.setCanvasImage()
      this.reset()
    },
    tool (value) {
      this[value]()
    }
  },

  data () {
    return {
      experimentResultID: null,
      pictureID: null,
      canvasContainer: null,
      image: null,
      points: [],
      savedShapes: [],
      deleteArea: [],
      // keeps track of whether the user is drawing or erasing
      TOOL: 'MARKER',
      // canvas where the drawing will take place
      canvas: null,
      ctx: null,
      // put our savedShapes on another canvas,
      // so we don't have to redraw them every time something changes
      savedCanvas: null,
      savedCtx: null
    }
  },

  mounted () {
    /* eslint-disable */

    // canvas where the drawing will take place
    this.canvas = this.$el.querySelector('#currentDrawing')
    this.ctx = this.canvas.getContext('2d')

    // put our savedShapes on another canvas,
    // so we don't have to redraw them every time something changes
    this.savedCanvas = this.$el.querySelector('#savedShapes')
    this.savedCtx = this.savedCanvas.getContext('2d')

    this.canvasContainer = this.$el

    // this.setCanvasImage()
  },

  methods: {
    /**
     * Enable canvas drawing, turn off panning.
     */
    setModeToDrawing () {
        this.enableMarking()

        // this.canvas.css({ cursor: "default" })
        this.canvas.style.cursor = 'default'

        // disable panning of the images so we can use the marking tool on the image
        $(".panzoom").panzoom("option", "disablePan", true)
    },

    /**
     * Enable panning, turn off canvas drawing.
     */
    setModeToPanning () {
        this.disableMarking()

        // this.canvas.css({ cursor: "move" })
        this.canvas.style.cursor = 'move'

        // disable panning of the images so we can use the marking tool on the image
        $(".panzoom").panzoom("option", "disablePan", false)
    },

    setEraseActiveTool () {
        this.TOOL = "DELETE"
    },

    setMarkingActiveTool () {
        this.TOOL = "MARKER"
    },

    /**
     * Enable the drawing events.
     */
    enableMarking () {
        this.canvas.addEventListener('mousedown', this.startdrag)
        this.canvas.addEventListener('mouseup', this.stopdrag)
        this.canvas.addEventListener('dblclick', this.findClickedShape)
    },

    /**
     * Disable the drawing events.
     */
    disableMarking () {
        this.canvas.removeEventListener('mousedown', this.startdrag)
        this.canvas.removeEventListener('mouseup', this.stopdrag)
        this.canvas.removeEventListener('dblclick', this.findClickedShape)
    },

    /**
     * Figure out the size of the image, so we can set the canvas to the same size.
     */
    setCanvasImage () {
        this.image = new Image()
        this.image.src = this.imageURL.path

        // mutations.setUpdated(false)

        var self = this
        this.image.onload = function() {
            // make all elements the same size as the image
            self.canvasContainer.style.height = self.image.height + 'px'
            self.canvasContainer.style.width = self.image.width + 'px'
            self.canvasContainer.style.position = 'relative'

            // make canvases the same size as the image
            self.canvas.setAttribute('height', self.image.height)
            self.canvas.setAttribute('width', self.image.width)

            self.savedCanvas.setAttribute('height', self.image.height)
            self.savedCanvas.setAttribute('width', self.image.width)

            self.savedCanvas.style.background = 'url(' + self.image.src + ')'
            self.savedCanvas.style.position = 'absolute'
            self.savedCanvas.style.top = '0'
            self.savedCanvas.style.right = '0'

            self.canvas.style.position = 'absolute'
            self.canvas.style.top = '0'
            self.canvas.style.right = '0'
        }
    },

    /**
     * Whenever a double click event takes place on the canvas this function
     * checks whether the click is inside a shape.
     *
     * Note: We have to redraw before we can call isPointInPath()
     */
    findClickedShape (e) {
        var mouseX  = e.offsetX
        var mouseY  = e.offsetY

        this.ctx.lineWidth = 2

        for (var k = 0; k < this.savedShapes.length; k++) {
            this.ctx.beginPath()
            this.ctx.moveTo(this.savedShapes[k].points.x, this.savedShapes[k].points.y)

            for (var d = 0; d < this.savedShapes[k].points.length; d++) {
                this.ctx.lineTo(this.savedShapes[k].points[d].x, this.savedShapes[k].points[d].y)
            }

            if (this.ctx.isPointInPath(mouseX, mouseY)) {
                // TODO: openAnnotationModal(k, this.savedShapes[k].annotation);
                console.log('clicked')
                break; /* we found the clicked polygon, no need to loop through the rest */
            }
            this.ctx.closePath();
        }
    },

    /**
     * Delete one shape, by removing the last array element.
     * If no shapes are saved, empty the current drawn shape.
     *
     * @return void
     */
    undo () {
        (this.points.length > 0) ? this.points = [] : this.savedShapes.pop()

        /* redraw to reflect the changes */
        this.draw()
        this.drawSavedShapes()
    },

    stopdrag (e) {
        this.canvasContainer.removeEventListener('mousemove', this.mousedrag)
        /* we're done drawing, save the shape */
        this.saveShape(e)
    },

    startdrag () {
        this.canvasContainer.addEventListener('mousemove', this.mousedrag)
    },

    /**
     * Calls the drawing function if the current mouse point
     * is atleast 6 pixels away from the last point (in either in the y or x direction).
     *
     * @return {boolean} false
     */
    mousedrag (e) {
        e.preventDefault()

        var dis
        var x = e.offsetX
        var y = e.offsetY

        for (var i = 0; i < this.points.length; i++) {
            dis = Math.sqrt(Math.pow(x - this.points[i].x, 2) + Math.pow(y - this.points[i].y, 2))
            if ( dis < 6 ) {
                // return out of this function if we do not have enough distance
                return false
            }
        }

        this.points.push({ x: Math.round(x), y: Math.round(y) })

        this.draw()

        return false
    },

    /**
     * Takes the values from the points array,
     * and draws a line between each point.
     * This function is called from the mousedrag function.
     *
     * @return void
     */
    draw () {
        /* clear the canvas each time */
        this.ctx.clearRect(0, 0, this.ctx.canvas.width, this.ctx.canvas.height)

        /* do not draw before we have atleast two points */
        if (this.points.length > 1) {
            this.ctx.fillStyle = 'rgba(0, 0, 0, 0.3)'
            this.ctx.strokeStyle = 'rgb(255, 255, 255)'
            this.ctx.lineWidth = 2

            this.ctx.beginPath()
            /* start the shape at the first coordinate in the array */
            this.ctx.moveTo(this.points[0].x, this.points[0].y)

            /* go through the array in in sequential order, drawing a line between each point */
            for (var i = 0; i < this.points.length; i++) {
                if (this.points.length > 1) {
                    this.ctx.lineTo(this.points[i].x, this.points[i].y)
                }
            }

            this.ctx.fill()
            this.ctx.stroke()
        }
    },

    /**
     * Draw all the shapes in the savedShapes array.
     *
     * @return void
     */
    drawSavedShapes () {
        /* clear the canvas each time */
        this.savedCtx.clearRect(0, 0, this.ctx.canvas.width, this.ctx.canvas.height);

        if (this.savedShapes.length > 0) {
            this.savedCtx.fillStyle = 'rgba(0, 100, 0, 0.6)';
            this.savedCtx.strokeStyle = 'rgba(0, 100, 0, 0.6)';
            this.savedCtx.lineWidth = 2;

            for (var k = 0; k < this.savedShapes.length; k++) {
                for (var d = 0; d < this.savedShapes[k].fill.length; d++) {
                    this.savedCtx.fillRect(this.savedShapes[k].fill[d].x, this.savedShapes[k].fill[d].y, 1, 1);
                }
            }
        }
    },

    /**
     * Save the shape by putting all the points from the points array into the savedShape array,
     * and then emptying the points array.
     *
     * @return void
     */
    saveShape (e) {
        // only save the shape if we have atleast 3 points
        if (this.points.length > 2) {
            if (this.TOOL == "MARKER") {
                // save all the x and y coordinates as well as any comment
                let fillCalculated = this.calcFill(this.points)
                this.savedShapes.push({
                    picture_id: this.imageURL.image.id,
                    points: this.points,
                    annotation: '',
                    fill: fillCalculated
                })

                if (this.savedShapes.length > 1) {
                    this.mergePossibleOverlapping()
                }
            } else if (this.TOOL == "DELETE") {
                let fillCalculated = this.calcFill(this.points)
                this.deleteArea.push({
                    picture_id: this.imageURL.image.id,
                    points: this.points,
                    annotation: '',
                    fill: fillCalculated
                })

                // for each point in the delete shape look for a match in existing shapes
                for (var i = 0; i < this.deleteArea[0].fill.length; i++) {
                    for (var j = 0; j < this.savedShapes.length; j++) {
                        for (var k = 0; k < this.savedShapes[j].fill.length; k++) {
                            /* if both X and Y matches in the savedShape and deleteArea array */
                            if ( this.savedShapes[j].fill[k].x == this.deleteArea[0].fill[i].x &&
                                 this.savedShapes[j].fill[k].y == this.deleteArea[0].fill[i].y ) {

                                this.savedShapes[j].fill.splice(k, 1);

                                break; /* we found a match, no need for more looping */
                            }
                        }
                    }
                }

                this.deleteArea = []
            }

            this.$emit('updated', { uuid: this.uuid, shapes: this.savedShapes })
        }

        this.points = [] /* remove the current shape now that it's saved */

        this.draw()
        this.drawSavedShapes()

        // $('.fa-spinner').remove();
    },

    /**
     * Search for overlapping coordinates if we have more than one shape.
     *
     * @return void
     */
    mergePossibleOverlapping () {
        var allShapesMerged = []
        var duplicates = []

        this.savedShapes.forEach(function(shape, i) {
            shape.fill.forEach(function(coord) {
                allShapesMerged.push(coord)
            })
        })

        // group coordinates into arrays, matching coordinates gets put into the same array
        var groups = lodashGroupBy(allShapesMerged, function(item) {
            return [item.x, item.y]
        })

        // for each array: if the array contains more than one item,
        // we have a duplicate values. Push a of those values into an array which will effectively
        // hold a list which values that occure more than once.
        lodashEach(groups, function(group) {
            if (group.length > 1) {
                duplicates.push.apply(duplicates, [group[0]]);
            }
        })

         for (var i = 0; i < duplicates.length; i++) {
            shapeLoop: for (var j = 0; j < this.savedShapes.length; j++) {
                for (var k = 0; k < this.savedShapes[j].fill.length; k++) {

                    /* if both X and Y matches in the savedShape and deleteArea array */
                    if ( this.savedShapes[j].fill[k].x == duplicates[i].x &&
                         this.savedShapes[j].fill[k].y == duplicates[i].y ) {

                        this.savedShapes[j].fill.splice(k, 1)

                        break shapeLoop /* we found a match, no need for more looping */
                    }
                }
            }
        }

        duplicates = []
        allShapesMerged = []
    },

    reset () {
        this.points = []
        this.savedShapes = []

        /* redraw to reflect the changes */
        this.draw()
        this.drawSavedShapes()
    },



    /*---------------------------------------------
          Fill Algorithm for Polygon
    ---------------------------------------------*/
    /**
     *  Remove duplicates from the matrix array.
     *  Used for multidimensional arrays.
     *  @param  {Array}   The current matrix.
     *  @return {Boolean} Returns an unique matrix.
     */
    removeDupeVerts (dataArray) {
      // This method is more effective rather than using For or For-each loops
      // URL: http://stackoverflow.com/questions/9229645/remove-duplicates-from-javascript-array
      // Answer by: georg | paragraph "Unique by..."
      // Viewed: 02.03.2016, 00:30.
      function uniqBy(a, key)
      {
        var seen = {};
        return a.filter(function(item)
        {
          var k = key(item);
          return seen.hasOwnProperty(k) ? false : (seen[k] = true);
        });
      }

      return uniqBy(dataArray, JSON.stringify);
    },

    /**
     * Fill Algorithm | Ray-casting
     * @param {Array}    Point(x,y) to check if it intersects with the polygon.
     * @param {Array}    The rectangle area container for the polygon.
     * @return  {Boolean}  Returns true if the point is inside the polygon.
     */
    pointInsidePolygon (point, vertices) {
      // ray-casting algorithm based on
      // http://www.ecse.rpi.edu/Homepages/wrf/Research/Short_Notes/pnpoly.html

      var px = point[0], py = point[1];

      var inside = false;
      var j = vertices.length - 1;

      var xi, yi, xj, yj;

      for (var i = 0; i < vertices.length; i++)
      {
        var xi = vertices[i][0], yi = vertices[i][1];
        var xj = vertices[j][0], yj = vertices[j][1];

        var intersect = ((yi > py) != (yj > py))
          && (px < (xj - xi) * (py - yi) / (yj - yi) + xi);

        if (intersect) {
          inside = !inside;
        }

        j = i;
      }
      return inside
    },

    /**
     * Calculate a rectangle of the polygon.
     * Optimizing purposes to avoid iterating the whole image matrix for each polygon.
     * @param  { Object }  Polygon object to calculate its rectangle.
     * @return { Array }   The vertices of the calculated rectangle.
     */
    polygonToRectangle (polygon) {
      // Return lowest value in array:
      Array.min = function( array ){ return Math.min.apply( Math, array ); }

      // Return highest value in array:
      Array.max = function( array ){ return Math.max.apply( Math, array ); }

      var x = [], y = []

      for(var i = 0; i < polygon.length; i ++)
      {
        x.push(polygon[i][0])
        y.push(polygon[i][1])
      }

      var rectVertices = [ Array.min(x), Array.min(y), Array.max(x), Array.max(y) ]

      return rectVertices
    },

    /**
     * Convert the polygon object vertices to array.
     * @param  {Object}  polygon object.
     * @return {Array}   polygon's vertices (x,y).
     */
    convertPolygonCoordToArray (polygon) {
      var array = []
      var tempArr = []

      for (var i = 0; i < polygon.length; i++) {
        tempArr = [polygon[i].x, polygon[i].y]
        array.push(tempArr)
      }

      return array
    },


    // TODO: MOVE THIS TO HEATMAP COMPONENT?
    /**
     * Calculate total pixel points for all polygons.
     * Estimates from the current savedShapes.
     * @return {Array} Array of every marked pixel.
     */
    calcPolygonPoints () {
      // Atleast one polygon exists:
      if (this.savedShapes.length > 0) {
        var allMarkedPoints = []; // Store all marked pixels.

        for (var i = 0; i < this.savedShapes.length; i++) {
          for(var j = 0; j < this.savedShapes[i].fill.length; j ++) {
            allMarkedPoints.push([this.savedShapes[i].fill[j].x, this.savedShapes[i].fill[j].y] )
          }
        }

        var matrix = createMatrix(allMarkedPoints) // Array with all matrixes and intersect value.

        return allMarkedPoints
      }
    },

    /**
     * Calculate all pixels inside a shape.
     *
     * @param  {Object}  A shape to find all marked pixels.
     * @return {Array}   Array of every marked pixel for this shape.
     */
    calcFill (shape) {
      var polygonRect = []       // Keeps the polygon rectangle vertices.
      var tempPolygonArr  = []   // Keeps a polygon's coordinates in a 2D array.
      var fillArray = []

      tempPolygonArr = this.convertPolygonCoordToArray(shape)   // Convert to array.
      polygonRect = this.polygonToRectangle(tempPolygonArr)     // Return array of vertices from the polygon's rectangle.

      // All 4 vertices of the rectangle:
      var rect_p1 = polygonRect[0], rect_p2 = polygonRect[1]
      var rect_p3 = polygonRect[2], rect_p4 = polygonRect[3]

      for(var i = 0; i <= this.image.width; i ++ )
      {
          for(var j = 0; j <= this.image.height; j++)
          {
              var point = [i,j] // Check this point.

              // The point is inside the polygon:
              if (this.pointInsidePolygon(point, tempPolygonArr)) {
                  fillArray.push( {x:i, y:j} )
              }
          }
      }

      return fillArray
    }

  }
}
</script>
