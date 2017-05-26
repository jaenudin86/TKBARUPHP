@extends('layouts.adminlte.master')

@section('title')
    @lang('tax.invoice.output.create.title')
@endsection

@section('page_title')
    <span class="fa fa-sign-out"></span>&nbsp;@lang('tax.invoice.output.create.page_title')
@endsection

@section('page_title_desc')
    @lang('tax.invoice.output.create.page_title_desc')
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('create_tax_invoice_output') !!}
@endsection

@section('content')
    <div id="taxVue">
        <form id="taxForm" class="form-horizontal">
            {{ csrf_field() }}
            <div class="row">
                <div class="col-md-6">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">Informasi Transaksi PPN</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputVATTranType" class="col-sm-4 control-label">Jenis Transaksi PPN</label>
                                <div class="col-sm-8">
                                    <select id="inputVATTranType" name="vat_transaction_type" class="form-control">
                                        <option v-bind:value="defaultVATTranType.code">@lang('labels.PLEASE_SELECT')</option>
                                        <option v-for="vtt of vatTranTypeDDL" v-bind:value="vtt.code">@{{ vtt.description }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputTransDoc" class="col-sm-4 control-label">Dokumen Transaksi</label>
                                <div class="col-sm-8">
                                    <select id="inputTransDoc" name="transaction_doc" class="form-control">
                                        <option v-bind:value="defaultTranDoc.code">@lang('labels.PLEASE_SELECT')</option>
                                        <option v-for="td of tranDocDDL" v-bind:value="td.code">@{{ td.description }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputDateOfTaxDoc" class="col-sm-4 control-label">Tanggal Dokumen Pajak</label>
                                <div class="col-sm-8">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <vue-datetimepicker id="inputDateOfTaxDoc" name="tax_doc_date" format="DD-MM-YYYY hh:mm A" @change="changeTaxPeriod"></vue-datetimepicker>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputTaxPeriod" class="col-sm-4 control-label">Masa Pajak</label>
                                <div class="col-sm-8">
                                    <input id="inputTaxPeriod" name="tax_period" type="text" class="form-control" v-bind:value="taxPeriod" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputTranDet" class="col-sm-4 control-label">Detail Transaksi PPN</label>
                                <div class="col-sm-8">
                                    <select id="inputTranDet" name="transaction_detail" class="form-control">
                                        <option v-bind:value="defaultTranDetail.code">@lang('labels.PLEASE_SELECT')</option>
                                        <option v-for="td of tranDetailDDL" v-bind:value="td.code">@{{ td.description }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">Lawan Transaksi</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputTaxIDNo" class="col-sm-2 control-label">NPWP</label>
                                <div class="col-sm-10">
                                    <input id="inputOpponentTaxIDNo" name="opponent_tax_id_no" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputOpponentName" class="col-sm-2 control-label">Nama</label>
                                <div class="col-sm-10">
                                    <input id="inputOpponentName" name="opponent_name" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputOpponentAddress" class="col-sm-2 control-label">Address</label>
                                <div class="col-sm-10">
                                    <textarea id="inputOpponentAddress" name="opponent_address" class="form-control" rows="5"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">Detail Transaksi</h3>
                            <button type="button" class="btn btn-primary btn-xs pull-right" v-on:click="insertTransaction()"><span class="fa fa-plus fa-fw"/></button>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <table id="detailTransactionListTable" class="table table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <th>Nama Barang Kena Pajak / Jasa Kena Pajak</th>
                                            <th class="text-center">Harga Satuan</th>
                                            <th class="text-center">Jumlah Barang</th>
                                            <th class="text-center">Harga Total</th>
                                            <th >&nbsp</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(tran, tranIndex) in taxOutput.transactions">
                                            <td>
                                                <input name="tran_name[]" type="text" class="form-control" v-model="tran.name">
                                            </td>
                                            <td>
                                                <input name="tran_price[]" type="text" class="form-control text-right" v-model="tran.price"/>
                                            </td>
                                            <td>
                                                <input name="tran_qty[]" type="text" class="form-control text-right" v-model="tran.qty"/>
                                            </td>
                                            <td>
                                                @{{ numeral(tran.price) }}
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-md"><span class="fa fa-minus" v-on:click="removeTransaction(tranIndex)"></span>
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <table id="transactionsTotalListTable" class="table table-bordered">
                                        <tbody>
                                        <tr>
                                            <td class="text-right">Total</td>
                                            <td class="text-right">
                                                <span class="control-label-normal"></span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
@endsection

@section('custom_js')
    <script type="application/javascript">

        Vue.component('vue-datetimepicker', {
            template: "<input type='text' v-bind:id='id' v-bind:name='name' class='form-control' v-bind:format='format' v-model='value'>",
            props: ['id', 'name', 'format'],
            data: function() {
                return {
                    value: ''
                };
            },
            mounted: function() {
                var vm = this;

                if (this.format == undefined) this.format = 'DD-MM-YYYY hh:mm A';
                if (this.readonly == undefined) this.readonly = 'false';

                $(this.$el).datetimepicker({
                    format: this.format,
                    defaultDate: this.value == '' ? moment():moment(this.value),
                    showTodayButton: true,
                    showClose: true
                })
                .on('dp.change', function(e) {
                    vm.$emit('change', e.date);
                });
            },
            destroyed: function() {
                $(this.$el).data("DateTimePicker").destroy();
            }
        });

        var poApp = new Vue({
            el: '#taxVue',
            data: {
                vatTranTypeDDL: JSON.parse('{!! htmlspecialchars_decode($vatTranTypeDDL) !!}'),
                tranDocDDL: JSON.parse('{!! htmlspecialchars_decode($tranDocDDL) !!}'),
                tranDetailDDL: JSON.parse('{!! htmlspecialchars_decode($tranDetailDDL) !!}'),
                taxPeriod: moment().format('MM/YYYY'),
                taxOutput: {
                    transactions: []
                }
            },
            methods: {
                changeTaxPeriod: function(e) {
                    this.taxPeriod = moment(e).format('MM/YYYY');
                },
                insertTransaction: function() {
                    var vm = this;
                    vm.taxOutput.transactions.push({
                        name: '',
                        amount: 0,
                        qty: 0,
                        total: 0,
                    });
                },
                removeTransaction: function (index) {
                    var vm = this;
                    vm.taxOutput.transactions.splice(index, 1);
                },
            },
            computed: {
                defaultVATTranType: function(){
                    return {
                        code: ''
                    };
                },
                defaultTranDoc: function(){
                    return {
                        code: ''
                    };
                },
                defaultTranDetail: function(){
                    return {
                        code: ''
                    };
                }
            }
        });

    </script>
@endsection
