import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { describe, expect, it, vi } from "vitest";
import RadioInput from "./RadioInput";

describe("RadioInput", () => {
  it("should render with label", () => {
    // Arrange
    render(
      <RadioInput
        id="test-radio"
        name="test"
        value="option1"
        label="Option 1"
        data-testid="radio"
      />,
    );
    const radio = screen.getByTestId("radio");

    // Assert
    expect(radio).toBeInTheDocument();
    expect(radio).toHaveAttribute("id", "test-radio");
    expect(radio).toHaveAttribute("name", "test");
    expect(radio).toHaveAttribute("value", "option1");
    expect(screen.getByText("Option 1")).toBeInTheDocument();
  });

  it("should handle user interaction", async () => {
    // Arrange
    const handleChange = vi.fn();
    render(
      <RadioInput
        id="test-radio"
        name="test"
        value="option1"
        label="Option 1"
        data-testid="radio"
        onChange={handleChange}
      />,
    );
    const radio = screen.getByTestId("radio");

    // Act
    await userEvent.click(radio);

    // Assert
    expect(handleChange).toHaveBeenCalled();
    expect(radio).toBeChecked();
  });

  it("should accept native HTML props", () => {
    // Arrange
    render(
      <RadioInput
        id="test-radio"
        name="test"
        value="option1"
        label="Option 1"
        data-testid="radio"
        disabled
        required
        checked
      />,
    );
    const radio = screen.getByTestId("radio");

    // Assert
    expect(radio).toHaveAttribute("disabled");
    expect(radio).toHaveAttribute("required");
    expect(radio).toBeChecked();
  });
});
