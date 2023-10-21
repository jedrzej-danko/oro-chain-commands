# Console command chaining

This repository contains a complete solution of "Console Command Chaining" task:

- an example minimalistic Symfony application in `/app` directory
- Console command chaining bundle, named `ChainCommandBundle` in `/chain-command-bundle` directory
- two example bundles: FooBundle and BarBundle in `/foo-bundle` and `/bar-bundle` directories

## Installation

1. Clone this repository
2. Enter `cd app` directory
3. Run `composer install`


## Configuration

Manual configuration may be provided by editing the `config/packages/chain_command.yaml` file.


```yaml
chain_command:

  chains:

    "debug:config": # chain master command name
      members:
        - debug:dotenv # chain member command name
```

However, bundles may self-register by modifying ChainCommandBundle configuration in their "Bundle" class. See `\OroChain\BarBundle\BarBundle` class for example.

## Theory of operation

Whole solution depends on the Symfony events. The  `\OroChain\ChainCommandBundle\EventListener\BeforeCommandListener` listens for the ConsoleCommandEvent, which is triggered just before the command execution. Listener checks if the current command is a chain member or chain master and behaves accordingly.

There are three simple rules for the command chaining:
1. If the command is not a chain master, nor it is a chain member, it is executed as usual.
2. If the command is a chain member, it can't be executed directly
3. If the command is a chain master, it is executed  instantly in the listener, and then all his chain members are executed in the same order as they were defined in the master command.

However, there are some additional rules about the error handling:

1. If the execution of chain master fails, none of his chain members are executed.
2. If the execution of chain member fails, none of the following chain members are executed.

### Logging

ChainCommandBundle creates its own logging channel, named `chained_command`. By default, this log creates separated log file in the `var/log` directory. The log consist of some debug information about command execution and the whole output of the executed command. If you don't want to log command output, you need to modify the implementation of `chain_command_bundle.console_output` service.

The default log format is different from the standard one. The formatting is done by the `\OroChain\ChainCommandBundle\Logger\LogFormatter` class. You may modify the default formatter by replacing the service of `chain_command_bundle.log_formatter` with your own implementation.

