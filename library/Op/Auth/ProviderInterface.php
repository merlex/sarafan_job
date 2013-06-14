<?php
/**
 * Интерфейс провайдера аутентификации:
 * @author 1
 *
 */
interface Op_Auth_ProviderInterface {
	/**
	 * Получить аутентификацию:
	 * @return void
	 */
	function authenticate($request, $url);
	/**
	 * id пользователя
	 * @return char
	 */
	function getID();
	/**
	 * имя пользователя
	 * @return array
	 */
	function getChanged();
	/**
	 * данные:
	 * @return array
	 */
	function getInfo();
}
?>