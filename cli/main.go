package main

import (
	"os"
	"os/exec"
)

func main() {
	args := append([]string{"bin/hyperf.php"}, os.Args[1:]...)
	cmd := exec.Command("php", args...)
	cmd.Stdin = os.Stdin
	cmd.Stdout = os.Stdout
	cmd.Stderr = os.Stderr
	if err := cmd.Run(); err != nil {
		os.Exit(1)
	}
}