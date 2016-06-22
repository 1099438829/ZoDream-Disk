<?php
defined('APP_DIR') or exit();
$this->extend(array(
    'layout' => array(
        'head'
    ))
);
?>

<div class="main">
<form method="post">
	<h3>设置您的数据库连接</h3>
	<p>请在下方填写您的数据库连接信息。如果您不确定，请联系您的服务提供商。</p>
	<table class="table table-hover">
		<tr>
			<th>数据库名</th>
			<td><input name="db[database]" class="form-control" type="text" size="25" required value="zodream" /></td>
			<td>安装到哪个数据库？</td>
		</tr>
		<tr>
			<th>用户名</th>
			<td><input name="db[user]" class="form-control" type="text" size="25" value="root" required placeholder="用户名" /></td>
			<td>您的MySQL用户名</td>
		</tr>
		<tr>
			<th>密码</th>
			<td><input name="db[password]" class="form-control" type="text" size="25" placeholder="密码" autocomplete="off" /></td>
			<td>&hellip;及其密码</td>
		</tr>
		<tr>
			<th>数据库主机</th>
			<td><input name="db[host]" class="form-control" type="text" size="25" value="localhost" /></td>
			<td>如果<code>localhost</code>不能用，您通常可以从网站服务提供商处得到正确的信息。</td>
		</tr>
        <tr>
			<th>数据库端口</th>
			<td><input name="db[port]" class="form-control" type="text" size="25" value="3306" /></td>
			<td>默认是3306</td>
		</tr>
		<tr>
			<th>表前缀</th>
			<td><input name="db[prefix]" class="form-control" type="text" value="zd_" size="25" /></td>
			<td>如果您希望在同一个数据库安装多个zodream，请修改前缀。</td>
		</tr>
	</table>
	<h3>创建管理员账号</h3>
	<table class="table table-hover">
		<tr>
			<th>用户名</th>
			<td><input name="user[name]" class="form-control" type="text" placeholder="用户名" required value="admin"/></td>
		</tr>
		<tr>
			<th>密码</th>
			<td><input name="user[password]" class="form-control" type="text" required placeholder="密码" /></td>
		</tr>
	</table>
	<h3>存储文件目录</h3>
	<input name="disk" class="form-control" type="text" placeholder="存储文件目录" required value="<?=APP_DIR.'\files'?>"/>
	<button class="btn btn-primary" type="submit">提交</button>
</form>
</div>

<?php
$this->extend(array(
	'layout' => array(
		'foot'
	))
);
?>