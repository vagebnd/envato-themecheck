<h1 align="center">Vagebond - Envato Theme checker</h1>

<p align="center">command-line tool to run envato theme checks against any folder</p>

<p align="center">
    <p align="center">
        <a href="//packagist.org/packages/vagebond/envato-theme-check"><img alt="Latest Stable Version" src="https://poser.pugx.org/vagebond/envato-theme-check/v"></a>
    </p>
</p>

## Instal

This CLI application is the envato-theme-check written in PHP and is installed using [Composer](https://getcomposer.org):

```
composer global require vagebond/envato-theme-check
```

Make sure the `~/.composer/vendor/bin` directory is in your system's `PATH`.

<details>
<summary>Show me how</summary>

If it's not already there, add the following line to your Bash configuration file (usually `~/.bash_profile`, `~/.bashrc`, `~/.zshrc`, etc.):

```
export PATH=~/.composer/vendor/bin:$PATH
```

If the file doesn't exist, create it.

Run the following command on the file you've just updated for the change to take effect:

```
source ~/.bash_profile
```
</details>

## Use

All you need to do is call the `check ~/path/to/theme` command to start the game:

## Update

```
composer global update vagebond/envato-theme-check
```

## Delete

```
composer global remove vagebond/envato-theme-check
```
