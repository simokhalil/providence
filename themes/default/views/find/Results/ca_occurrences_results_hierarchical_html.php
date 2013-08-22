<?php
/* ----------------------------------------------------------------------
 * themes/default/views/find/ca_occurrences_results_editable_html.php 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2013 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 *
 * This source code is free and modifiable under the terms of 
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * ----------------------------------------------------------------------
 */
	JavascriptLoadManager::register('viz');
	
	
	$t_display				= $this->getVar('t_display');
	$va_display_list 		= $this->getVar('display_list');
	$vo_result 				= $this->getVar('result');
	$vn_items_per_page 		= $this->getVar('current_items_per_page');
	$pn_type_id 			= $this->getVar('type_id');
	$vs_current_sort 		= $this->getVar('current_sort');
	$vs_default_action		= $this->getVar('default_action');
	$vo_ar					= $this->getVar('access_restrictions');
	
	$va_initial_data 		= $this->getVar('initialData');

	$o_result_context = $this->getVar('result_context');

	$va_data = array('name' => $o_result_context->getSearchExpression(), 'row_id' => 0, 'idno' => null, 'childCount' => null, 'children' => array());
	$vn_item_count = 0;
	
	$t_occ = new ca_occurrences();
	while(($vn_item_count < $vn_items_per_page) && $vo_result->nextHit()) {
		$vn_occurrence_id = $vo_result->get('occurrence_id');

		if($vo_ar->userCanAccess($this->request->user->getUserID(), array("editor","occurrences"), "OccurrenceEditor", "Edit", array("occurrence_id" => $vn_occurrence_id))){
			$vs_action = "Edit";
		} else {
			$vs_action = "Summary";
		}
		
		$va_data['children'][] = array(
			'name' => $vo_result->get('ca_occurrences.preferred_labels.name'),
			'idno' => $vo_result->get('ca_occurrences.idno'),
			'row_id' => $vn_occurrence_id,
			'editUrl' => caNavUrl($this->request, 'editor/occurrences', 'OccurrenceEditor', $vs_action, array('occurrence_id' => $vn_occurrence_id)),
			'children' => array(),
			'childCount' => sizeof($t_occ->getHierarchyChildren($vn_occurrence_id, array('idsOnly' => true)))
		);
		
		$i++;
		$vn_item_count++;
	}
	//print_r(json_encode($va_data, JSON_PRETTY_PRINT));
?>
<div id="chart" style="overflow: auto;"></div>

<script type="text/javascript">
	var theData = <?php print json_encode($va_data); ?>;
	
	var w = 600,
    h = 500,
    i = 0,
    barHeight = 30,
    barWidth = w * .8,
    duration = 250,
    root;
 
var tree = d3.layout.tree()
    .size([h, 100]);
 
var diagonal = d3.svg.diagonal()
    .projection(function(d) { return [d.y, d.x]; });
 
var vis = d3.select("#chart").append("svg:svg")
    .attr("width", w)
    .attr("height", h)
  .append("svg:g")
    .attr("transform", "translate(20,30)");


theData.x0 = 0;
theData.y0 = 0;
update(root=theData);
 
 
function update(source) {
 
  // Compute the flattened node list. TODO use d3.layout.hierarchy.
  var nodes = tree.nodes(root);
  
  // Compute the "layout".
  nodes.forEach(function(n, i) {
    n.x = i * barHeight;
  });
  
  // Update the nodes…
  var node = vis.selectAll("g.node")
      .data(nodes, function(d) { return d.id || (d.id = ++i); });
  
  var nodeEnter = node.enter().append("svg:g")
      .attr("class", "node")
      .attr("transform", function(d) { return "translate(" + source.y0 + "," + source.x0 + ")"; })
      .style("opacity", 1e-6);
 
  // Enter any new nodes at the parent's previous position.
  nodeEnter.append("svg:rect")
      .attr("y", -barHeight / 2)
      .attr("height", barHeight)
      .attr("width", barWidth)
      .style("fill", color)
      .on("click", click);
  
  nodeEnter
  	.append("svg:text")
      .attr("dy", 3.5)
      .attr("dx", 15)
      .text(function(d) { return d.name + (d.idno ? " (" + d.idno + ")" : "")+ ((d.childCount > 0) ? " [" + d.childCount + "]" : ""); });
      
  nodeEnter
  	.append("svg:a").attr("xlink:href", function(d) { return d.editUrl; })
  	.append("svg:image")
      .attr("y", -8)
      .attr("x", barWidth - 20)
      .attr("xlink:href", function(d) { return (d.row_id > 0) ? "/admin/themes/default/graphics/buttons/edit.png" : ""; }).attr("width", 16).attr("height", 16);
  
  // Transition nodes to their new position.
  nodeEnter.transition()
      .duration(duration)
      .attr("transform", function(d) { return "translate(" + d.y + "," + d.x + ")"; })
      .style("opacity", 1);
  
  node.transition()
      .duration(duration)
      .attr("transform", function(d) { return "translate(" + d.y + "," + d.x + ")"; })
      .style("opacity", 1)
    .select("rect")
      .style("fill", color);
  
  // Transition exiting nodes to the parent's new position.
  node.exit().transition()
      .duration(duration)
      .attr("transform", function(d) { return "translate(" + source.y + "," + source.x + ")"; })
      .style("opacity", 1e-6)
      .remove();
  
  // Update the links…
  var link = vis.selectAll("path.link")
      .data(tree.links(nodes), function(d) { return d.target.id; });
  
  // Enter any new links at the parent's previous position.
  link.enter().insert("svg:path", "g")
      .attr("class", "link")
      .attr("d", function(d) {
        var o = {x: source.x0, y: source.y0};
        return diagonal({source: o, target: o});
      })
    .transition()
      .duration(duration)
      .attr("d", diagonal);
  
  // Transition links to their new position.
  link.transition()
      .duration(duration)
      .attr("d", diagonal);
  
  // Transition exiting nodes to the parent's new position.
  link.exit().transition()
      .duration(duration)
      .attr("d", function(d) {
        var o = {x: source.x, y: source.y};
        return diagonal({source: o, target: o});
      })
      .remove();
  
  // Stash the old positions for transition.
  nodes.forEach(function(d) {
    d.x0 = d.x;
    d.y0 = d.y;
  });
  
  var curHeight = (nodes.length * 30) + 50;
  jQuery("#chart svg").height((curHeight > 500) ? curHeight : 500);
}
 
// Toggle children on click.
function click(d) {
  if (!d.row_id) { return; }
  if (!d.children || !d.children.length) {
  	if (!d._children) {
  		// load from ajax
  		d3.json("<?php print caNavUrl($this->request, $this->request->getModulePath(), $this->request->getController(), 'VizGetChildren', array('download' => 1, 'id' => '')); ?>" + d.row_id, function(json) {
  			d._children = null;
			d.children = json; 
		  	update(d);
		});
		return;
  	}
    d._children = d.children;
    d.children = null;
  } else {
    d.children = d._children;
    d._children = null;
  }
  update(d);
}
 
function color(d) {
	return '#e0e0e0';
}
</script>