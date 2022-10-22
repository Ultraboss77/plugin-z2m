<?php
/* This file is part of Plugin zigbee for jeedom.
*
* Plugin zigbee for jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Plugin zigbee for jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Plugin zigbee for jeedom. If not, see <http://www.gnu.org/licenses/>.
*/
if (!isConnect('admin')) {
    throw new Exception('401 Unauthorized');
}
$plugin = plugin::byId('z2m');
$infos = z2m::getDeviceInfo('bridge1');
$map = z2m::getDeviceInfo('networkMap1');
sendVarToJS('z2m_network_map', $map);
?>
<script type="text/javascript" src="plugins/zigbee/3rdparty/vivagraph/vivagraph.min.js"></script>
<style>
    #graph_network {
        height: 80%;
        width: 90%;
        position: absolute;
    }

    #graph_network>svg {
        height: 100%;
        width: 100%
    }

    .node-item {
        border: 1px solid;
    }

    .zigbee-purple {
        color: #a65ba6;
    }

    .zigbee-green {
        color: #7BCC7B;
    }

    .node-remote-control-color {
        color: #00a2e8;
    }

    .zigbee-yellow {
        color: #E5E500;
    }

    .node-more-of-two-up-color {
        color: #FFAA00;
    }

    .node-interview-not-completed-color {
        color: #979797;
    }

    .zigbee-red {
        color: #d20606;
    }

    .node-na-color {
        color: white;
    }

    #graph_network svg g text {
        fill: var(--txt-color) !important;
    }
</style>
<select class="pull-right form-control" id="sel_networkZigbeeInstance" style="width:250px;">
    <?php
    foreach (z2m::getDeamonInstanceDef() as $zigbee_instance) {
        if ($zigbee_instance['enable'] != 1) {
            continue;
        }
        echo '<option value="' . $zigbee_instance['id'] . '">' . $zigbee_instance['name'] . '</option>';
    }
    ?>
</select>
<ul id="tabs_network" class="nav nav-tabs" data-tabs="tabs">
    <li class="active"><a href="#application_network" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Application}}</a></li>
    <li><a href="#devices_network" data-toggle="tab"><i class="fab fa-codepen"></i> {{Noeuds}} (<?php echo count($infos['config']['devices']) ?>)</a></li>
    <li role="presentation" id="tab_graph"><a href="#graph_network" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-list-alt"></i> {{Graphique du réseaux}}</a></li>
    <li role="presentation"><a href="#rawBridgeTab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-list-alt"></i> {{Informations brutes}}</a></li>
</ul>

<div id="network-tab-content" class="tab-content">
    <div class="tab-pane active" id="application_network">
        <br />
        <form class="form-horizontal">
            <fieldset>
                <br>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4 class="panel-title"><i class="fas fa-info-circle"></i> {{Zigbee2mqtt}}</h4>
                    </div>
                    <div class="panel-body">
                        <p>
                            {{Version :}}
                            <b><span class="label label-default" style="font-size : 1em;"><?php echo $infos['version'] ?></span></b>
                            {{Log :}}
                            <b><span class="label label-default" style="font-size : 1em;"><?php echo $infos['log_level'] ?></span></b>
                            <br />
                            {{Coordinateur :}}
                            <b><span class="label label-default" style="font-size : 1em;"><?php echo $infos['coordinator']['type'] ?></span></b>
                            {{Port :}}
                            <b><span class="label label-default" style="font-size : 1em;"><?php echo $infos['config']['serial']['port'] ?></span></b>
                            <br />
                            {{Interval de verification OTA :}}
                            <b><span class="label label-default" style="font-size : 1em;"><?php echo $infos['config']['ota']['update_check_interval'] ?>s</span></b>
                        </p>
                    </div>
                </div>

                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4 class="panel-title"><i class="fas fa-info-circle"></i> {{Réseaux zigbee}}</h4>
                    </div>
                    <div class="panel-body">
                        <p>
                            {{Canal :}}
                            <b><span class="label label-default" style="font-size : 1em;"><?php echo $infos['network']['channel'] ?></span></b>
                            {{Ext pan id :}}
                            <b><span class="label label-default" style="font-size : 1em;"><?php echo $infos['network']['extended_pan_id'] ?></span></b>
                            {{Pan id :}}
                            <b><span class="label label-default" style="font-size : 1em;"><?php echo $infos['network']['pan_id'] ?></span></b>
                        </p>
                    </div>
                </div>


                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4 class="panel-title"><i class="fas fa-info-circle"></i> {{MQTT}}</h4>
                    </div>
                    <div class="panel-body">
                        <p>
                            {{Topic :}}
                            <b><span class="label label-default" style="font-size : 1em;"><?php echo $infos['config']['mqtt']['base_topic'] ?></span></b>
                            {{Serveur :}}
                            <b><span class="label label-default" style="font-size : 1em;"><?php echo $infos['config']['mqtt']['server'] ?></span></b>
                            {{Utilisateur :}}
                            <b><span class="label label-default" style="font-size : 1em;"><?php echo $infos['config']['mqtt']['user'] ?></span></b>
                        </p>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>

    <div role="tabpanel" class="tab-pane" id="devices_network">
        <br />
        <table class="table table-bordered table-condensed">
            <thead>
                <th>
                <td>{{ID}}</td>
                <td>{{Nom}}</td>
                </th>
            </thead>
            <tbody>
                <?php
                foreach ($infos['config']['devices'] as $device_id => $device_info) {
                    $eqLogic = eqLogic::byLogicalId(z2m::convert_to_addr($device_id), 'z2m');
                    echo '<tr>';
                    echo '<td>';
                    if (is_object($eqLogic)) {
                        $child = ($eqLogic->getConfiguration('ischild', 0) == 1) ? '<i style="position:absolute;font-size:1.5rem!important;right:10px;top:10px;" class="icon_orange fas fa-user" title="Ce device est un enfant"></i>' : '';
                        $child .= ($eqLogic->getConfiguration('canbesplit', 0) == 1 && $eqLogic->getConfiguration('ischild', 0) == 0) ? '<i style="position:absolute;font-size:1.5rem!important;right:10px;top:10px;" class="icon_green fas fa-random" title="Ce device peut être séparé en enfants"></i>' : '';


                        if ($eqLogic->getConfiguration('device') != "") {
                            if (z2m::getImgFilePath($eqLogic->getConfiguration('device'), $eqLogic->getConfiguration('manufacturer')) !== false && $eqLogic->getConfiguration('ischild', 0) == 0) {
                                echo '<img class="lazy" src="plugins/z2m/core/config/devices/' . z2m::getImgFilePath($eqLogic->getConfiguration('device'), $eqLogic->getConfiguration('manufacturer')) . '" height="40" width="40"/>' . $child;
                            } else if ($eqLogic->getConfiguration('ischild', 0) == 1 && file_exists(dirname(__FILE__) . '/../../core/config/devices/' . $eqLogic->getConfiguration('visual', 'none'))) {
                                echo '<img class="lazy" src="plugins/z2m/core/config/devices/' . $eqLogic->getConfiguration('visual') . '" height="40" width="40"/>' . $child;
                            } else if ($eqLogic->getConfiguration('ischild', 0) == 1 && z2m::getImgFilePath($eqLogic->getConfiguration('device'), $eqLogic->getConfiguration('manufacturer')) !== false) {
                                echo '<img class="lazy" src="plugins/z2m/core/config/devices/' . z2m::getImgFilePath($eqLogic->getConfiguration('device'), $eqLogic->getConfiguration('manufacturer')) . '" height="40" width="40"/>' . $child;
                            } else {
                                echo '<img src="' . $plugin->getPathImgIcon() . '" height="40" width="40" />' . $child;
                            }
                        } else {
                            echo '<img src="' . $plugin->getPathImgIcon() . '" height="40" width="40" />' . $child;
                        }
                    }
                    echo '</td>';
                    echo '<td>';
                    echo z2m::convert_to_addr($device_id);
                    echo '</td>';
                    echo '<td>';
                    if (is_object($eqLogic)) {
                        echo '<a href="index.php?v=d&p=z2m&m=z2m&id=' . $eqLogic->getId() . '" >' . $eqLogic->getHumanName() . '</a>';
                    }
                    echo '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <div role="tabpanel" class="tab-pane" id="rawBridgeTab">
        <pre><?php echo json_encode($map, JSON_PRETTY_PRINT); ?></pre>
        <pre><?php echo json_encode($infos, JSON_PRETTY_PRINT); ?></pre>

    </div>

    <div id="graph_network" class="tab-pane">
        <br />
        <a class="btn bt-default btn-sm pull-right" id="bt_networkMapUpdate"><i class="fas fa-sync"></i> {{Mettre à jour}}</a>
        <table class="table table-bordered table-condensed" style="width: 350px;position:fixed;margin-top : 25px;">
            <thead>
                <tr>
                    <th colspan="2">{{Légende}}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="color:var(--al-danger-color)">
                        <center><i class="fas fa-square fa-2x"></i></center>
                    </td>
                    <td>{{Mauvaise liaison}}</td>
                </tr>
                <tr>
                    <td style="color:var(--al-warning-color)">
                        <center><i class="fas fa-square fa-2x"></i></center>
                    </td>
                    <td>{{Liaison correcte}}</td>
                </tr>
                <tr>
                    <td style="color:var(--al-success-color)">
                        <center><i class="fas fa-square fa-2x"></i></center>
                    </td>
                    <td>{{Très bonne laison}}</td>
                </tr>
                <tr></tr>
                <tr>
                    <td style="color:#a65ba6">
                        <center><i class="fas fa-circle"></i></center>
                    </td>
                    <td>{{Gateway}}</td>
                </tr>
                <tr>
                    <td style="color:#00a2e8">
                        <center><i class="fas fa-circle"></i></center>
                    </td>
                    <td>{{Coordinateur}}</td>
                </tr>
                <tr>
                    <td style="color:#E5E500">
                        <center><i class="fas fa-circle"></i></center>
                    </td>
                    <td>{{Routeur}}</td>
                </tr>
                <tr>
                    <td style="color:#7BCC7B">
                        <center><i class="fas fa-circle"></i></center>
                    </td>
                    <td>{{End device}}</td>
                </tr>
            </tbody>
        </table>
        <div id="graph-node-name"></div>

    </div>
</div>


<script>
    $('#bt_networkMapUpdate').off('click').on('click', function() {
        jeedom.z2m.bridge.updateNetworkMap({
            instance: 1,
            error: function(error) {
                $('#div_alert').showAlert({
                    message: error.message,
                    level: 'danger'
                });
            },
            success: function() {
                $('#div_alert').showAlert({
                    message: '{{Demande de mise à jour de la carte réseaux envoyée. Veuillez attendre 3min et reouvrir la carte}}',
                    level: 'success'
                });
            }
        });
    });

    $("#tab_graph").off("click").one("click", function() {
        controler_ieee = null
        for (z in z2m_network_map.nodes) {
            if (z2m_network_map.nodes[z].networkAddress == 0) {
                controler_ieee = z2m_network_map.nodes[z].ieeeAddr
            }
        }
        $('#graph_network svg').remove();
        z2m_network_map_ok = {}
        var graph = Viva.Graph.graph();
        for (z in z2m_network_map.nodes) {
            if (z2m_network_map.nodes[z].ieeeAddr == '' || z2m_network_map.nodes[z].networkAddress == null) {
                continue;
            }
            var img = '';
            if (z2m_devices[jeedom.z2m.utils.convert_to_addr(z2m_network_map.nodes[z].ieeeAddr)]) {
                img = z2m_devices[jeedom.z2m.utils.convert_to_addr(z2m_network_map.nodes[z].ieeeAddr)].img;
            }

            let data_node = {
                'ieee': z2m_network_map.nodes[z].ieeeAddr,
                'name': (z2m_devices[jeedom.z2m.utils.convert_to_addr(z2m_network_map.nodes[z].ieeeAddr)]) ? z2m_devices[jeedom.z2m.utils.convert_to_addr(z2m_network_map.nodes[z].ieeeAddr)].HumanName : z2m_network_map.nodes[z].ieeeAddr,
                'type': z2m_network_map.nodes[z].type,
                'networkAddress': z2m_network_map.nodes[z].networkAddress,
                'modelID': z2m_network_map.nodes[z].modelID,
                'manufacturerName': z2m_network_map.nodes[z].manufacturerName,
                'img': img,
                'offline': (z2m_network_map.nodes[z].failed) ? true : false
            }
            if (isset(z2m_devices[z2m_network_map.nodes[z].ieeeAddr])) {
                data_node.name = z2m_devices[jeedom.z2m.utils.convert_to_addr(z2m_network_map.nodes[z].ieeeAddr)].HumanName
            } else if (z2m_network_map.nodes[z].networkAddress == 0) {
                data_node.name = '{{Controleur}}';
            }
            if (z2m_network_map.nodes[z].networkAddress == 0) {
                data_node.name = '{{Contrôleur}}'
            }
            graph.addNode(z2m_network_map.nodes[z].ieeeAddr, data_node);
        }

        for (z in z2m_network_map.links) {
            let lqi = z2m_network_map.links[z].lqi;
            linkcolor = '#B7B7B7';
            if (lqi > 120) {
                linkcolor = 'var(--al-success-color)';
            } else if (lqi > 85) {
                linkcolor = 'var(--al-warning-color)';
            } else if (lqi > 0) {
                linkcolor = 'var(--al-danger-color)';
            }
            graph.addLink(z2m_network_map.links[z].sourceIeeeAddr, z2m_network_map.links[z].targetIeeeAddr, {
                color: linkcolor,
                lengthfactor: (lqi / 255) * 1.1
            });
        }

        var graphics = Viva.Graph.View.svgGraphics()
        highlightRelatedNodes = function(nodeId, isOn) {
            graph.forEachLinkedNode(nodeId, function(node, link) {
                var linkUI = graphics.getLinkUI(link.id);
                if (linkUI) {
                    linkUI.attr('stroke-width', isOn ? '2.2px' : '1px');
                }
            });
        };
        var nodeSize = 24
        graphics.node(function(node) {
            if (typeof node.data == 'undefined') {
                graph.removeNode(node.id);
                return;
            }
            nodecolor = '#5F6A6A';
            var nodesize = 10;
            const nodeshape = 'rect';
            if (node.data.networkAddress == '0x0000') {
                nodecolor = '#a65ba6';
                nodesize = 24;
            } else if (node.data.type == 'Coordinator') {
                nodesize = 16;
                nodecolor = '#00a2e8';
            } else if (node.data.type == 'EndDevice') {
                nodecolor = '#7BCC7B';
            } else if (node.data.type == 'Router') {
                nodesize = 16;
                nodecolor = '#E5E500';
            }
            var ui = Viva.Graph.svg('g'),
                svgText = Viva.Graph.svg('text').text(node.data.name),
                img = Viva.Graph.svg('image')
                .attr('width', 48)
                .attr('height', 48)
                .link(node.data.img);
            ui.append(svgText);
            ui.append(img);
            circle = Viva.Graph.svg('circle')
                .attr('r', 7)
                .attr('cx', -10)
                .attr('cy', -4)
                .attr('stroke', '#fff')
                .attr('stroke-width', '1.5px')
                .attr('fill', nodecolor);
            ui.append(circle);
            $(ui).hover(function() {
                if (z2m_devices[jeedom.z2m.utils.convert_to_addr(node.data.ieee)] && z2m_devices[jeedom.z2m.utils.convert_to_addr(node.data.ieee)].id) {
                    linkname = '<a href="index.php?v=d&p=z2m&m=z2m&id=' + z2m_devices[jeedom.z2m.utils.convert_to_addr(node.data.ieee)].id + '">' + node.data.name + '</a>'
                } else {
                    linkname = node.data.name
                }
                linkname += ' <span class="label label-primary" title="{{Type}}">' + node.data.type + '</span>'
                if (node.data.manufacturerName && node.data.modelID) {
                    linkname += ' <span class="label label-primary" title="{{Modèle}}">' + node.data.manufacturerName + ' ' + node.data.modelID + '</span>'
                    linkname += ' <span class="label label-primary" title="{{NWK}}">' + node.data.networkAddress + '</span>'
                }

                $('#graph-node-name').html(linkname);
                highlightRelatedNodes(node.id, true);
            }, function() {
                highlightRelatedNodes(node.id, false);
            });
            return ui;
        }).placeNode(function(nodeUI, pos) {
            nodeUI.attr('transform',
                'translate(' +
                (pos.x - 24) + ',' + (pos.y - 24) +
                ')');
        });
        var idealLength = 400;
        var layout = Viva.Graph.Layout.forceDirected(graph, {
            springLength: idealLength,
            stableThreshold: 0.9,
            dragCoeff: 0.05,
            springCoeff: 0.0004,
            gravity: -20,
            springTransform: function(link, spring) {
                spring.length = idealLength * (1 - link.data.lengthfactor);
            }
        });
        graphics.link(function(link) {
            dashvalue = '5, 0';
            if (link.data.isdash == 1) {
                dashvalue = '5, 2';
            }
            return Viva.Graph.svg('line').attr('stroke', link.data.color).attr('stroke-dasharray', dashvalue).attr('stroke-width', '2px');
        });
        $('#graph_network svg').remove();
        var renderer = Viva.Graph.View.renderer(graph, {
            layout: layout,
            graphics: graphics,
            prerender: 10,
            renderLinks: true,
            container: document.getElementById('graph_network')
        });
        renderer.run();
        setTimeout(function() {
            renderer.pause();
            renderer.reset();
        }, 200);
    });
</script>