import { render, screen } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { describe, expect, it, vi } from "vitest";
import RadioGroup from "./RadioGroup";

const mockOptions = [
  { value: "option1", label: "Option 1" },
  { value: "option2", label: "Option 2" },
  { value: "option3", label: "Option 3" },
];

describe("RadioGroup", () => {
  it("should render all options from array", async () => {
    // Arrange
    const handleChange = vi.fn();
    render(
      <RadioGroup
        name="test"
        options={mockOptions}
        value="option1"
        onChange={handleChange}
        label="Choose an option"
      />,
    );

    // Assert
    expect(screen.getByText("Choose an option")).toBeInTheDocument();
    expect(screen.getByText("Option 1")).toBeInTheDocument();
    expect(screen.getByText("Option 2")).toBeInTheDocument();
    expect(screen.getByText("Option 3")).toBeInTheDocument();

    const option1 = screen.getByLabelText("Option 1");
    expect(option1).toBeChecked();

    // Act
    await userEvent.click(screen.getByLabelText("Option 2"));

    // Assert
    expect(handleChange).toHaveBeenCalledWith("option2");
  });
});
