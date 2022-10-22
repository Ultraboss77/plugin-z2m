<?php
/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

try {
  require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
  include_file('core', 'authentification', 'php');

  if (!isConnect('admin')) {
    throw new Exception(__('401 - Accès non autorisé', __FILE__));
  }

  ajax::init();

  if (init('action') == 'include') {
    mqtt2::publish(z2m::getInstanceTopic(init('instance')) . '/bridge/request/permit_join', '{"value": true, "time": 180}');
    ajax::success();
  }

  if (init('action') == 'setDeviceOptions') {
    $eqLogic = eqLogic::byId(init('id'));
    if (!is_object($eqLogic)) {
      throw new Exception(__('Equipement introuvable : ', __FILE__) . init('id'));
    }
    $eqLogic->setDeviceOptions(json_decode(init('options'), true));
    ajax::success();
  }

  if (init('action') == 'publish') {
    mqtt2::publish(z2m::getInstanceTopic(init('instance')) . init('topic'), init('message'));
    ajax::success();
  }

  throw new Exception(__('Aucune méthode correspondante à', __FILE__) . ' : ' . init('action'));
  /*     * *********Catch exeption*************** */
} catch (Exception $e) {
  ajax::error(displayException($e), $e->getCode());
}
