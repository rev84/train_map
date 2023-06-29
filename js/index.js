const LINE_SELECTOR_ID = 'line_selector';
const DRAW_STATIONS_ID = 'draw_stations';

const CIRCLE_RADIUS = 10;
const CIRCLE_STROKE_WIDTH = CIRCLE_RADIUS * 0.4;
const LINE_WIDTH = CIRCLE_RADIUS * 1.6;

document.addEventListener('DOMContentLoaded', function() {
  initialize();
});

function initialize()
{
  setLineSelect();
}

function setLineSelect()
{
  $('#'+LINE_SELECTOR_ID).html('');
  var select = $('<select>').addClass('form-control');
  select.append(
    $('<option>').attr('value', '').text('-')
  );
  for (var lineCd in DATA.lines) {
    const name = DATA.lines[lineCd];
    select.append(
      $('<option>').attr('value', lineCd).text(name)
    );
  }
  select.on('change', function(self){
    onLineSelect(self.target.value);
  });

  $('#'+LINE_SELECTOR_ID).append(select);
}

function onLineSelect(lineCd)
{
  if (lineCd == '') {
    return;
  }
  var stations = getStations(lineCd);
  drawStations(stations);
}

function drawStations(stations)
{
  $('#'+DRAW_STATIONS_ID).html('')

  var data = stations2Data(stations);

  var svg = d3.select('#'+DRAW_STATIONS_ID).append("svg")
  .attr("width", 3000)
  .attr("height", 1000);

  var lineGenerator = d3.line()
  .x(function(d) { return d.x; })
  .y(function(d) { return d.y; });

  var pathString = lineGenerator(data);

  svg.append("path")
  .attr('d', pathString)
  .style("stroke", "#80c342")
  .style("stroke-width", LINE_WIDTH)
  .style("fill", "none");

  svg.selectAll("circle")
    .data(data)
    .enter()
    .append("circle")
    .attr("cx", function(d) { return d.x; })
    .attr("cy", function(d) { return d.y; })
    .attr("r", CIRCLE_RADIUS)
    .style("fill", "white")
    .style("stroke", "black")
    .style("stroke-width", CIRCLE_STROKE_WIDTH);

  svg.selectAll("foreignObject")
    .data(data)
    .enter()
    .append("foreignObject")
    .attr("x", function(d) { return d.x - 15; })
    .attr("y", function(d) { return d.y + CIRCLE_RADIUS + 5; }) 
    .attr("width", 30)
    .attr("height", 600)
    .append("xhtml:div")
    .attr("class", "station")
    .text(function(d) { return d.name; })
}

function stations2Data(stations)
{
  var results = [];
  for (var idx in stations) {
    var station = stations[idx];
    results.push({
      name: station.station_name,
      x: (50 + idx * 50),
      y: 100,
    })
  }
  return results;
}

function getStations(lineCd)
{
  var results = [];
  for (var idx in DATA.stations) {
    var station = DATA.stations[idx];
    if (station.line_cd == lineCd) {
      results.push(station);
    }
  }

  return results;
}