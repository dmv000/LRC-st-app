<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessageTables extends Migration
{
    protected $messageTable = 'message';
    protected $messageGroupsTable = 'message_groups';
    protected $userMessagesTable = 'user_message';

    public function up()
    {
        Schema::create($this->messageTable, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->string('subject');
            $table->text('content');
            $table->enum('response_type', ['free', 'predefined'])->default('free');
            $table->text('response_options')->nullable(); // Comma-separated or JSON
            $table->integer('user_id');
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->timestamps();

            $table->index(['user_id'], 'user_id_idx');

            $table->foreign('user_id', $this->messageTable . '_user_id_fk')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        Schema::create($this->messageGroupsTable, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('message_id')->unsigned();
            $table->integer('group_id');
            $table->timestamps();

            $table->index(['message_id', 'group_id'], 'message_group_idx');
            $table->index(['group_id'], 'group_id_idx');

            $table->foreign('message_id', $this->messageGroupsTable . '_message_id_fk')
                ->references('id')->on($this->messageTable)
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('group_id', $this->messageGroupsTable . '_group_id_fk')
                ->references('id')->on('group')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        Schema::create($this->userMessagesTable, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('message_id')->unsigned();
            $table->integer('user_id');
            $table->enum('status', ['sent', 'failed']);
            $table->string('reason')->nullable();
            $table->string('identifier')->nullable();
            $table->text('response')->nullable();
            $table->string('ack')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();

            $table->index(['message_id', 'user_id'], 'message_user_idx');
            $table->index(['user_id'], 'user_id_idx');

            $table->foreign('message_id', $this->userMessagesTable . '_message_id_fk')
                ->references('id')->on($this->messageTable)
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('user_id', $this->userMessagesTable . '_user_id_fk')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists($this->messageGroupsTable);
        Schema::dropIfExists($this->userMessagesTable);
        Schema::dropIfExists($this->messageTable);
    }
}
