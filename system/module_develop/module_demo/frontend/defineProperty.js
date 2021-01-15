
  if (!Element.prototype.hasOwnProperty('some_prop')) {
    Object.defineProperty(Element.prototype, 'some_prop', {
      configurable: true,
      enumerable  : true,
      writable    : true,
      value: 'some value'
    });
  }

  if (!Element.prototype.hasOwnProperty('some_prop')) {
    Object.defineProperty(Element.prototype, 'some_prop', {
      configurable: true,
      enumerable  : true,
      get: function() {
        return 'some value from getter';
      }
    });
  }

  if (!Element.prototype.hasOwnProperty('some_prop')) {
    Object.defineProperty(Element.prototype, 'some_prop', {
      configurable: true,
      enumerable  : true,
      writable    : true,
      value: function() {
        return 'some value from method';
      }
    });
  }

  console.log( Object.getOwnPropertyDescriptor(Element.prototype, 'some_prop').value        );
  console.log( Object.getOwnPropertyDescriptor(Element.prototype, 'some_prop').configurable );
  console.log( Object.getOwnPropertyDescriptor(Element.prototype, 'some_prop').enumerable   );
  console.log( Object.getOwnPropertyDescriptor(Element.prototype, 'some_prop').writable     );
  console.log( Element.prototype.propertyIsEnumerable('some_prop') );
