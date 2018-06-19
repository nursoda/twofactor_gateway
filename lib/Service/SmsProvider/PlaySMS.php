<?php

declare(strict_types = 1);

/**
 * @author Pascal Clémot <pascal.clemot@free.fr>
 *
 * Nextcloud - Two-factor Gateway
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OCA\TwoFactorGateawy\Service\SmsProvider;

use Exception;
use OCA\TwoFactorGateawy\Exception\SmsTransmissionException;
use OCA\TwoFactorGateawy\Service\ISmsService;
use OCP\Http\Client\IClient;
use OCP\Http\Client\IClientService;
use OCP\IConfig;

class PlaySMS implements ISmsService {

	/** @var IClient */
	private $client;

	/** @var IConfig */
	private $config;

	public function __construct(IClientService $clientService, IConfig $config) {
		$this->client = $clientService->newClient();
		$this->config = $config;
	}

	/**
	 * @throws SmsTransmissionException
	 */
	public function send(string $recipient, string $message) {
		$url = $this->config->getAppValue('twofactor_gateway', 'playsms_url');
		$user = $this->config->getAppValue('twofactor_gateway', 'playsms_user');
		$password = $this->config->getAppValue('twofactor_gateway', 'playsms_password');
		try {
			$this->client->get($url, [
				'query' => [
					'app' => 'ws',
					'u' => $user,
					'h' => $password,
					'op' => 'pv',
					'to' => $recipient,
					'msg' => $message,
				],
			]);
		} catch (Exception $ex) {
			throw new SmsTransmissionException();
		}
	}

}
