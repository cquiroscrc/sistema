
var element = document.getElementById('selector');

var dispatchMask = IMask(element, {
    mask: [
      {
        mask: '0*** *** *** *******************',
        startsWith: '1',
        lazy: false,
        placeholderChar: '#' 
      },
      {
        mask: '0***** ***** *******',
        startsWith: '2',      
        lazy: false,              
        placeholderChar: '#' 
      },
      {
        mask: '0***********************',
        startsWith: '3',
        lazy: false,              
        placeholderChar: '#'        
      },
      {
        mask: '0****** ***** *****',
        startsWith: '4',
        lazy: false,              
        placeholderChar: '#'
      },
      {
        mask: '0*** *** *****',
        startsWith: '5',
        lazy: false,              
        placeholderChar: '#'        
      },
      {
        mask: '0*************************',
        startsWith: '6',
        lazy: false,              
        placeholderChar: '#'        
      },
      {
        mask: '0**********************',
        startsWith: '9',
        lazy: false        
      },
      {
        mask: '0**********************',
        startsWith: '0',
        lazy: false     
      }
    ],
    
    dispatch: function (appended, dynamicMasked) {
      var number = (dynamicMasked.value + appended).replace(/\D/g,'');
      return dynamicMasked.compiledMasks.find(function (m) {
        return number.indexOf(m.startsWith) === 0;
      });
    }
  }
)
var element2 = document.getElementById('selector2');
var dispatchMask2 = IMask(element2, {
      mask: [
        {
          mask: '0*** *** *** *******************',
          startsWith: '1',
          lazy: false,
          placeholderChar: '#'
        },
        {
          mask: '0***** ***** *******',
          startsWith: '2',
          lazy: false,
          placeholderChar: '#'
        },
        {
          mask: '0***********************',
          startsWith: '3',
          lazy: false,
          placeholderChar: '#'
        },
        {
          mask: '0****** ***** *****',
          startsWith: '4',
          lazy: false,
          placeholderChar: '#'
        },
        {
          mask: '0*** *** *****',
          startsWith: '5',
          lazy: false,
          placeholderChar: '#'
        },
        {
          mask: '0*************************',
          startsWith: '6',
          lazy: false,
          placeholderChar: '#'
        },
        {
          mask: '0**********************',
          startsWith: '9',
          lazy: false
        },
        {
          mask: '0**********************',
          startsWith: '0',
          lazy: false
        }
      ],

      dispatch: function (appended, dynamicMasked) {
        var number = (dynamicMasked.value + appended).replace(/\D/g,'');
        return dynamicMasked.compiledMasks.find(function (m) {
          return number.indexOf(m.startsWith) === 0;
        });
      }
    }
)


function	log () {
		console.log(dispatchMask.masked._value);
        console.log(dispatchMask2.masked._value);
};

dispatchMask.on("accept", log);
dispatchMask.on("complete", log)
console.log(dispatchMask.unmaskedValue);

dispatchMask2.on("accept", log);
dispatchMask2.on("complete", log)
console.log(dispatchMask2.unmaskedValue);
