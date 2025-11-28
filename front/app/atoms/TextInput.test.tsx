import { fireEvent, render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { describe, expect, it } from "vitest";
import TextInput from "./TextInput";

describe("TextInput", () => {
  it("should render user input", async () => {
    // Arrange
    render(<TextInput id="testing" data-testid="input" />);
    const input = screen.getByTestId("input");

    // Act
    await userEvent.type(input, "Lorem ipsum");

    // Assert
    expect(input).toBeInTheDocument();
    expect(input).toHaveAttribute("id", "testing");
    expect(input).toHaveValue("Lorem ipsum");
  });

  it("should apply selected variant", () => {
    // Arrange
    render(<TextInput id="testing" data-testid="input" variant="full" />);
    const input = screen.getByTestId("input");

    // Assert
    expect(input).toHaveClass("w-full");
  });

  it("should accept native HTML props", () => {
    // Arrange
    render(
      <TextInput
        id="testing"
        data-testid="input"
        placeholder="Write here"
        disabled
        required
      />,
    );
    const input = screen.getByTestId("input");

    // Assert
    expect(input).toHaveAttribute("placeholder", "Write here");
    expect(input).toHaveAttribute("disabled");
    expect(input).toHaveAttribute("required");
  });
});
