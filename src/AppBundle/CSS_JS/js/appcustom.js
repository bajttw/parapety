// Calculate functions
function m2Area(mmArea) {
  return mmArea / 1000000;
}

function mmAreaConvert(mmArea, precision) {
  return B.str.float(m2Area(mmArea), precision || 3);
}

function mmPositonArea(posData, lack) {
  var posFields = Bajt.entity.getFields("Positions"),
    q = Bajt.getFieldValue(posData, "quantity", posFields),
    w = Bajt.getFieldValue(posData, "width", posFields),
    l = Bajt.getFieldValue(posData, "length", posFields),
    d = Bajt.getFieldValue(posData, "doubleSide", posFields),
    area = q * w * l;
  if (lack && (d || d > 0)) return area + area;
  return area;
}

function mmPositionsSummary(positions, byField) {
  var posFields = Bajt.entity.getFields("Positions"),
    _emptySum = function () {
      return {
        quantity: 0,
        area: 0,
        areal: 0
      };
    },
    summary = {
      all: _emptySum()
    },
    _sum = function (pos) {
      var val = _emptySum();
      if (Bajt.obj.is(pos)) {
        var q = Bajt.getFieldValue(pos, "quantity", posFields),
          w = Bajt.getFieldValue(pos, "width", posFields),
          l = Bajt.getFieldValue(pos, "length", posFields),
          d = Bajt.getFieldValue(pos, "doubleSide", posFields),
          area = q * w * l;
        val.quantity = q;
        val.area = area;
        val.areal = area + (d || d > 0 ? area : 0);
      }
      if (byField) {
        var field = Bajt.getFieldValue(pos, byField, posFields);
        field = Bajt.obj.is(field) ? field.id || field.v : field;
        if (field) {
          if (!(field in summary)) summary[field] = _emptySum();
          for (var f in val) {
            summary[field][f] += val[f];
          }
        }
      }
      for (var f in val) {
        summary.all[f] += val[f];
      }
    };
  if ($.isArray(positions)) {
    for (var i in positions) {
      _sum(positions[i]);

      // var
      //     pos=positions[i],
      //     d = Bajt.getFieldValue(pos, 'doubleSide', posFields),
      //     val = _emptySum();
      // val.quantity = Bajt.getFieldValue(pos, 'quantity', posFields),
      //     val.area = mmPositonArea(pos),
      //     val.areal = val.area + (d || d > 0 ? val.area : 0);
      // if (byField) {
      //     var field = Bajt.getFieldValue(pos, byField, posFields);
      //     field=Bajt.obj.is(field) ? field.id || field.v : field;
      //     _sum(field, val);
      // }
      //     _sum('all', val);
    }
  } else {
    _sum(positions);
  }
  return summary;
}

function summaryObjects(obj1, obj2) {
  if (!Bajt.obj.is(obj1) || $.isEmptyObject(obj2)) {
    return $.Bajt.obj.is(obj2) ? obj2 : {};
  } else if (Bajt.obj.is(obj2) || !$.isEmptyObject(obj2)) {
    for (var key in obj2) {
      if (key in obj1) {
        obj1[key] = Bajt.obj.is(obj2[key]) ?
          summaryObjects(obj1[key], obj2[key]) :
          obj1[key] + obj2[key];
      } else {
        obj1[key] = obj2[key];
      }
    }
  }
  return obj1;
}
// end Calculate functions

function ordersCheckParameters(order, options) {
  var oValues = {
    opt: {}
  },
    oFields = Bajt.entity.getFields("Orders"),
    fn = ["model", "handle", "size", "positionLackers", "cutter", "doubleSide"];
  for (var i = 0, ien = fn.length; i < ien; i++) {
    var n = fn[i],
      val;
    oValues[n] = Bajt.obj.getValue(n, options, {});
    if (n === "positionLackers") {
      // oValues[n]['opt'] = {
      //     sameVal: 'domyślnie'
      // };
      val = Bajt.getFieldValue(order, "lackers", oFields);
    } else val = Bajt.getFieldValue(order, n, oFields);
    oValues[n]["value"] = Bajt.json.is(val) ? JSON.parse(val) : val;
  }
  return oValues;
}

(function ($) {
  $.fn.createProduction = function () {
    var $btn = $(this),
      btn = $btn.data(),
      dt = dataTables['orders'],
      selected = dt.rows({ selected: true }),
      rows = DT.convertRowsData(selected, btn.options);
    selected.deselect();
    console.log(rows);
  };

})(jQuery);



