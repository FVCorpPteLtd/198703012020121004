<?php

/* 
 * ***************************************************************
 * Script : 
 * Version : 
 * Date :
 * Author : Fransiscus
 * Email : frans_1387@hotmail.com
 * Description : 
 * ***************************************************************
 */
?>
<div class="row">
	<div class="col-lg-12">
		<div id="cc" class="easyui-layout" style="width:100%;height:520px;">
			<div data-options="region:'center',title:'List Data Rekrutmen',split:true" style="width:100%;height:100px;background:#eee;">
				<table id="dg_show" class="easyui-datagrid" style="width:100%;height:440px" showFooter="true" singleSelect="true">
					<thead>
						<tr>
							<th field="id" width="2%" align="center">ID</th>
							<th field="timestamp" width="8%" align="center">TGL PENDAFATARAN</th>
							<th field="nama" width="10%" halign="center">NAMA</th>
							<th field="satuan_kerja" width="10%" halign="center">SATKER</th>
							<th field="posisi_yang_dipilih" width="10%" halign="center">POSISI</th>
							<th field="bahasa_pemrograman_yang_dikuasai" width="10%" halign="center">PEMROGRAMAN DIKUASI</th>
							<th field="framework_bahasa_pemrograman_yang_dikuasai" width="10%" halign="center">FRAMEWORK</th>
							<th field="database_yang_dikuasai" width="10%" halign="center">DATABASE</th>
							<th field="tools_yang_dikuasai" width="10%" halign="center">TOOLS</th>
							<th field="pernah_membuat_mobile_apps" width="5%" align="center">MOBILE APPS</th>
							<th field="nilai_t1" width="5%" halign="center">NILAI T1</th>
							<th field="nilai_t2" width="5%" halign="center">NILAI T2</th>
							<th field="nilai_t3" width="5%" halign="center">NILAI T3</th>
						</tr>
					</thead>
				</table>
			</div>


		</div>
		<script type="text/javascript">
			$(function() {
				Number.prototype.format = function(n, x, s, c) {
					var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
						num = this.toFixed(Math.max(0, ~~n));

					return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
				};

				var dg = $('#dg_show').datagrid({
					url: "<?php echo base_url('Laporan/get_data_rekrutmen'); ?>",
					pagination: true,
					remoteFilter: false,
					rownumbers: true,
					singleSelect: true,
					pageSize: 30,
					pageList: [30, 60, 90, 120, 150],
					fitColumns: true,
					fit: true,
					remoteSort: false,
					nowrap: false,
				});

				dg.datagrid('enableFilter', [{
					field: 'nilai_t1',
					type: 'numberbox',
					options: {
						precision: 0
					},
					op: ['equal', 'less', 'greater']
				}, {
					field: 'nilai_t2',
					type: 'numberbox',
					options: {
						precision: 0
					},
					op: ['equal', 'less', 'greater']
				}, {
					field: 'nilai_t3',
					type: 'numberbox',
					options: {
						precision: 0
					},
					op: ['equal', 'less', 'greater']
				}]);

				var pager = $('#dg_show').datagrid('getPager'); // get the pager of datagrid
				pager.pagination({
					showPageList: false,
					buttons: [{
						iconCls: 'icon-excel',
						handler: function() {
							doExcelFilter();
						}
					}],

				});

			});

			function doExcelFilter() {
				var rows = $('#dg_show').datagrid('getData').filterRows;
				$('#dg_show').datagrid('toExcel', {
					filename: 'Data_rekrutmen.xls',
					worksheet: 'Worksheet',
					caption: 'Data_rekrutmen',
					rows: rows
				});
				//$('#dg_show').datagrid('toExcel', 'Data_rekrutmen.xls');
			}
		</script>
		</section>