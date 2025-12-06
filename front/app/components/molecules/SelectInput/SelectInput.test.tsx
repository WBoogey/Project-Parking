import { render, screen } from "@testing-library/react";
import { describe, expect, it } from "vitest";
import SelectInput from "./SelectInput";
import userEvent from "@testing-library/user-event";

const mockChoices = ["Option 1", "Option 2", "Option 3"];

describe("SelectInput", () => {
  it("should render with default parameters", () => {
    // Arrange
    const { container } = render(
      <SelectInput placeholder="Select an option" choices={mockChoices} />,
    );
    const dropdown = container.querySelector(".absolute.min-w-52");

    // Assert
    expect(screen.getByText("Select an option")).toBeInTheDocument();
    expect(dropdown).toBeInTheDocument();
    expect(dropdown).toHaveClass("max-h-0 opacity-0 pointer-events-none");
  });

  it("should toggle on click", async () => {
    // Arrange
    const { container } = render(
      <SelectInput placeholder="Select an option" choices={mockChoices} />,
    );
    const user = userEvent.setup();
    const button = screen.getByTestId("select-trigger");

    // Act & Assert 1
    await user.click(button);

    const openDropdown = container.querySelector(".absolute.min-w-52");
    expect(openDropdown).toHaveClass("opacity-100");

    // Act & Assert 2
    await user.click(button);
    const closedDropdown = container.querySelector(".absolute.min-w-52");
    expect(closedDropdown).toHaveClass("max-h-0 opacity-0 pointer-events-none");
  });

  it("should render all choices in dropdown", () => {
    // Arrange
    render(
      <SelectInput placeholder="Select an option" choices={mockChoices} />,
    );

    // Assert
    mockChoices.forEach((choice) => {
      expect(screen.getByText(choice)).toBeInTheDocument();
    });
  });

  it("should select a choice and close dropdown", async () => {
    // Arrange
    const { container } = render(
      <SelectInput placeholder="Select an option" choices={mockChoices} />,
    );
    const user = userEvent.setup();
    const triggerButton = screen.getByTestId("select-trigger");

    // Act & Assert 1
    await user.click(triggerButton);

    const openDropdown = container.querySelector(".absolute.min-w-52");
    expect(openDropdown).toHaveClass("opacity-100");

    // Act & Assert 2
    const choiceButton = screen.getByTestId("select-choice-Option 1");
    await user.click(choiceButton);

    const triggerAfterSelection = screen.getByTestId("select-trigger");
    expect(triggerAfterSelection).toHaveTextContent("Option 1");
    const closedDropdown = container.querySelector(".absolute.min-w-52");
    expect(closedDropdown).toHaveClass("max-h-0 opacity-0 pointer-events-none");
  });

  it("should close dropdown when clicking outside", async () => {
    // Arrange
    const { container } = render(
      <div>
        <SelectInput placeholder="Select an option" choices={mockChoices} />
        <div data-testid="outside">Outside element</div>
      </div>,
    );
    const user = userEvent.setup();
    const triggerButton = screen.getByTestId("select-trigger");

    // Act & Assert 1
    await user.click(triggerButton);

    const openDropdown = container.querySelector(".absolute.min-w-52");
    expect(openDropdown).toHaveClass("opacity-100");

    // Act & Assert 2
    const outsideElement = screen.getByTestId("outside");
    await user.click(outsideElement);

    const closedDropdown = container.querySelector(".absolute.min-w-52");
    expect(closedDropdown).toHaveClass("max-h-0 opacity-0 pointer-events-none");
  });

  it.each([
    { variant: "sm", widthClass: "w-36", positionClass: "left-0" },
    { variant: "md", widthClass: "w-60", positionClass: "left-0" },
    { variant: "lg", widthClass: "w-96", positionClass: "right-0" },
    { variant: "full", widthClass: "w-full", positionClass: "right-0" },
  ] as const)(
    "should apply correct width and position for variant $variant",
    ({ variant, widthClass, positionClass }) => {
      // Arrange
      const { container } = render(
        <SelectInput
          placeholder="Select an option"
          choices={mockChoices}
          variant={variant}
        />,
      );
      const selectContainer = container.querySelector(".relative");
      const dropdown = container.querySelector(".absolute.min-w-52");

      // Assert
      expect(selectContainer).toHaveClass(widthClass);
      expect(dropdown).toHaveClass(positionClass);
    },
  );
});
